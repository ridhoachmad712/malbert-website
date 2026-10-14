<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\SchoolProfile;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class ManageController extends Controller
{
    private const CONTENT_TYPES = [
        'news' => ['label' => 'Berita', 'description' => 'Kabar dan liputan kegiatan sekolah.'],
        'announcement' => ['label' => 'Pengumuman', 'description' => 'Informasi resmi dengan masa berlaku bila diperlukan.'],
        'event' => ['label' => 'Agenda', 'description' => 'Kegiatan mendatang dengan tanggal dan lokasi.'],
        'gallery' => ['label' => 'Galeri', 'description' => 'Kumpulan foto kegiatan sekolah.'],
        'program' => ['label' => 'Program', 'description' => 'Penjelasan program pembelajaran dan kegiatan sekolah.'],
        'facility' => ['label' => 'Fasilitas', 'description' => 'Informasi ruang dan sarana sekolah.'],
        'achievement' => ['label' => 'Prestasi', 'description' => 'Capaian yang telah diperiksa sebelum diterbitkan.'],
        'faq' => ['label' => 'Tanya jawab', 'description' => 'Pertanyaan umum beserta jawabannya.'],
    ];

    public function dashboard(Request $request): View
    {
        return view('manage.dashboard', ['school' => SchoolProfile::current(), 'drafts' => Post::whereIn('status', ['draft', 'review'])->when($request->user()->role === 'writer', fn ($q) => $q->where('user_id', $request->user()->id))->latest()->take(8)->get(), 'postCount' => Post::count(), 'teacherCount' => Teacher::where('is_published', true)->count()]);
    }

    public function posts(Request $request): View
    {
        return $this->postList($request);
    }

    public function newPost(): View
    {
        return view('manage.post-chooser', ['school' => SchoolProfile::current(), 'contentTypes' => self::CONTENT_TYPES]);
    }

    public function postsByType(Request $request, string $type): View
    {
        $this->contentType($type);

        return $this->postList($request, $type);
    }

    public function newPostByType(string $type): View
    {
        $this->contentType($type);

        return view('manage.post-form', ['school' => SchoolProfile::current(), 'post' => new Post(['type' => $type]), 'contentType' => self::CONTENT_TYPES[$type]]);
    }

    public function editPost(Request $request, Post $post): View
    {
        $this->authorizePost($request, $post);

        return view('manage.post-form', ['school' => SchoolProfile::current(), 'post' => $post, 'contentType' => $this->contentType($post->type)]);
    }

    public function previewPost(Request $request, Post $post): View
    {
        $this->authorizePost($request, $post);

        return view('site.post', ['school' => SchoolProfile::current(), 'post' => $post, 'type' => ['news' => 'berita', 'announcement' => 'pengumuman', 'gallery' => 'galeri', 'program' => 'program', 'facility' => 'fasilitas', 'event' => 'agenda', 'achievement' => 'prestasi', 'faq' => 'faq'][$post->type], 'preview' => true]);
    }

    public function storePost(Request $request, string $type): RedirectResponse
    {
        $this->contentType($type);
        $post = new Post(['user_id' => $request->user()->id, 'type' => $type]);
        $this->savePost($request, $post);

        return redirect()->route('manage.posts.type', $type)->with('success', 'Draf '.$this->contentType($type)['label'].' tersimpan.');
    }

    public function updatePost(Request $request, Post $post): RedirectResponse
    {
        $this->authorizePost($request, $post);
        abort_if($request->user()->role === 'writer' && $post->status === 'published', 403);
        $this->savePost($request, $post);

        return redirect()->route('manage.posts.type', $post->type)->with('success', 'Konten diperbarui.');
    }

    public function postStatus(Request $request, Post $post): RedirectResponse
    {
        $this->authorizePost($request, $post);
        $action = $request->validate(['action' => ['required', Rule::in(['review', 'publish', 'draft', 'archive'])]])['action'];
        abort_if($request->user()->role === 'writer' && ! ($action === 'review' && $post->status === 'draft'), 403);
        abort_if($request->user()->role !== 'writer' && $action === 'review', 403);
        $post->status = match ($action) {
            'review' => 'review', 'publish' => 'published', 'archive' => 'archived', default => 'draft'
        };
        if ($action === 'publish') {
            $post->published_at ??= now();
        }
        $post->save();

        return back()->with('success', 'Status konten diperbarui.');
    }

    public function teachers(): View
    {
        $this->adminOnly();

        return view('manage.teachers', ['school' => SchoolProfile::current(), 'teachers' => Teacher::orderBy('sort_order')->orderBy('name')->paginate(30)]);
    }

    public function newTeacher(): View
    {
        $this->adminOnly();

        return view('manage.teacher-form', ['school' => SchoolProfile::current(), 'teacher' => new Teacher]);
    }

    public function editTeacher(Teacher $teacher): View
    {
        $this->adminOnly();

        return view('manage.teacher-form', ['school' => SchoolProfile::current(), 'teacher' => $teacher]);
    }

    public function storeTeacher(Request $request): RedirectResponse
    {
        $this->adminOnly();
        $this->saveTeacher($request, new Teacher);

        return redirect()->route('manage.teachers')->with('success', 'Profil guru tersimpan.');
    }

    public function updateTeacher(Request $request, Teacher $teacher): RedirectResponse
    {
        $this->adminOnly();
        $this->saveTeacher($request, $teacher);

        return redirect()->route('manage.teachers')->with('success', 'Profil guru diperbarui.');
    }

    public function editProfile(): View
    {
        $this->adminOnly();

        return view('manage.profile', ['school' => SchoolProfile::current()]);
    }

    public function editAppearance(): View
    {
        $this->adminOnly();

        return view('manage.appearance', ['school' => SchoolProfile::current()]);
    }

    public function updateAppearance(Request $request): RedirectResponse
    {
        $this->adminOnly();
        $rules = [
            'logo' => ['nullable', 'image', 'mimes:png,jpg,jpeg,webp', 'max:2048'],
            'hero' => ['nullable', 'image', 'mimes:png,jpg,jpeg,webp', 'max:6144'],
            'remove_logo' => ['nullable', 'boolean'],
            'remove_hero' => ['nullable', 'boolean'],
            'hero_title' => ['nullable', 'string', 'max:120'],
            'hero_highlight' => ['nullable', 'string', 'max:120'],
            'hero_description' => ['nullable', 'string', 'max:350'],
            'hero_primary_label' => ['nullable', 'string', 'max:40'],
            'hero_primary_route' => ['nullable', 'required_if:hero_primary_target_type,route', Rule::in(array_keys(SchoolProfile::HERO_LINKS))],
            'hero_primary_target_type' => ['required', Rule::in(['route', 'page', 'external'])],
            'hero_primary_page_id' => ['nullable', 'required_if:hero_primary_target_type,page', Rule::exists('site_pages', 'id')->where('is_published', true)],
            'hero_primary_external_url' => ['nullable', 'required_if:hero_primary_target_type,external', 'url:https', 'max:500'],
            'hero_primary_enabled' => ['required', 'boolean'],
            'hero_secondary_label' => ['nullable', 'string', 'max:40'],
            'hero_secondary_route' => ['nullable', 'required_if:hero_secondary_target_type,route', Rule::in(array_keys(SchoolProfile::HERO_LINKS))],
            'hero_secondary_target_type' => ['required', Rule::in(['route', 'page', 'external'])],
            'hero_secondary_page_id' => ['nullable', 'required_if:hero_secondary_target_type,page', Rule::exists('site_pages', 'id')->where('is_published', true)],
            'hero_secondary_external_url' => ['nullable', 'required_if:hero_secondary_target_type,external', 'url:https', 'max:500'],
            'hero_secondary_enabled' => ['required', 'boolean'],
            'hero_image_position' => ['required', Rule::in(['left', 'center', 'right'])],
            'hero_overlay' => ['required', Rule::in(['soft', 'normal', 'strong'])],
            'navigation_more_label' => ['required', 'string', 'max:24'],
            'home_sections' => ['required', 'array'],
            'theme_primary_color' => ['required', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'theme_accent_color' => ['required', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'theme_font' => ['required', Rule::in(['jakarta', 'system', 'serif'])],
            'header_style' => ['required', Rule::in(['standard', 'compact'])],
            'card_style' => ['required', Rule::in(['rounded', 'square'])],
            'hero_style' => ['required', Rule::in(['full', 'compact'])],
        ];
        foreach (array_keys(SchoolProfile::HOME_SECTIONS) as $key) {
            $rules["home_sections.$key.enabled"] = ['required', 'boolean'];
            $rules["home_sections.$key.order"] = ['required', 'integer', 'min:0', 'max:999'];
        }
        $data = $request->validate($rules);

        $school = SchoolProfile::current();
        foreach (['logo', 'hero'] as $image) {
            if ($request->hasFile($image)) {
                $data[$image.'_path'] = $request->file($image)->storePublicly('school', 'public');
            } elseif ($request->boolean('remove_'.$image)) {
                $data[$image.'_path'] = null;
            }
            unset($data[$image], $data['remove_'.$image]);
        }
        foreach (array_keys(SchoolProfile::HOME_SECTIONS) as $key) {
            $data['home_sections'][$key] = [
                'enabled' => (bool) $data['home_sections'][$key]['enabled'],
                'order' => (int) $data['home_sections'][$key]['order'],
            ];
        }
        foreach (['primary', 'secondary'] as $position) {
            $type = $data['hero_'.$position.'_target_type'];
            if ($type !== 'page') {
                $data['hero_'.$position.'_page_id'] = null;
            }
            if ($type !== 'external') {
                $data['hero_'.$position.'_external_url'] = null;
            }
        }
        $school->update($data);

        return back()->with('success', 'Tampilan website diperbarui.');
    }

    public function updateProfile(Request $request): RedirectResponse
    {
        $this->adminOnly();
        $data = $request->validate([
            'name' => ['required', 'string', 'max:180'], 'tagline' => ['nullable', 'string', 'max:220'],
            'npsn' => ['nullable', 'digits:8'], 'description' => ['nullable', 'string', 'max:3000'],
            'history' => ['nullable', 'string', 'max:5000'], 'vision' => ['nullable', 'string', 'max:2000'],
            'mission' => ['nullable', 'string', 'max:3000'], 'programs' => ['nullable', 'string', 'max:3000'],
            'facilities' => ['nullable', 'string', 'max:3000'], 'address' => ['nullable', 'string', 'max:800'],
            'email' => ['nullable', 'email', 'max:180'], 'phone' => ['nullable', 'string', 'max:80'],
            'hours' => ['nullable', 'string', 'max:180'], 'map_url' => ['nullable', 'url:http,https', 'max:500'],
            'aksikita_url' => ['nullable', 'url:https', 'max:500'],
            'principal_name' => ['nullable', 'string', 'max:180'], 'principal_greeting' => ['nullable', 'string', 'max:5000'],
            'principal_photo_height' => ['nullable', 'integer', 'between:'.SchoolProfile::PRINCIPAL_PHOTO_HEIGHT_MIN.','.SchoolProfile::PRINCIPAL_PHOTO_HEIGHT_MAX],
            'admission_intro' => ['nullable', 'string', 'max:3000'], 'admission_requirements' => ['nullable', 'string', 'max:5000'],
            'admission_steps' => ['nullable', 'string', 'max:5000'], 'admission_schedule' => ['nullable', 'string', 'max:3000'],
            'admission_url' => ['nullable', 'url:http,https', 'max:500'],
            'logo' => ['nullable', 'image', 'max:2048'], 'hero' => ['nullable', 'image', 'max:4096'],
            'principal_photo' => ['nullable', 'image', 'mimes:png', 'max:4096'],
            'remove_principal_photo' => ['nullable', 'boolean'],
        ]);
        foreach (['logo', 'hero'] as $image) {
            if ($request->hasFile($image)) {
                $data[$image.'_path'] = $request->file($image)->storePublicly('school', 'public');
            }
            unset($data[$image]);
        }
        $school = SchoolProfile::current();
        $previousPrincipalPhoto = $school->principal_photo_path;
        if ($request->hasFile('principal_photo')) {
            $data['principal_photo_path'] = $request->file('principal_photo')->storePublicly('school', 'public');
        } elseif ($request->boolean('remove_principal_photo')) {
            $data['principal_photo_path'] = null;
        }
        unset($data['principal_photo'], $data['remove_principal_photo']);
        $school->update($data);
        if ($previousPrincipalPhoto && array_key_exists('principal_photo_path', $data)) {
            Storage::disk('public')->delete($previousPrincipalPhoto);
        }

        return back()->with('success', 'Profil sekolah diperbarui.');
    }

    public function users(): View
    {
        $this->adminOnly();

        return view('manage.users', ['school' => SchoolProfile::current(), 'users' => User::orderBy('name')->get()]);
    }

    public function storeUser(Request $request): RedirectResponse
    {
        $this->adminOnly();
        $data = $request->validate([
            'name' => ['required', 'string', 'max:180'], 'email' => ['required', 'email', 'max:180', 'unique:users,email'],
            'role' => ['required', Rule::in(['admin', 'editor', 'writer'])],
            'password' => ['required', 'string', 'min:12', 'confirmed'],
        ]);
        User::create($data);

        return back()->with('success', 'Akun pengelola dibuat. Sampaikan kredensial melalui saluran privat.');
    }

    public function account(): View
    {
        return view('manage.account', ['school' => SchoolProfile::current()]);
    }

    public function updatePassword(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'string', 'min:12', 'confirmed'],
        ]);
        $request->user()->update(['password' => Hash::make($data['password'])]);

        return back()->with('success', 'Kata sandi diperbarui.');
    }

    private function savePost(Request $request, Post $post): void
    {
        $this->contentType($post->type);
        $rules = [
            'title' => ['required', 'string', 'max:180'],
            'body' => ['required', 'string', 'max:30000'],
        ];
        if ($post->type !== 'faq') {
            $rules['summary'] = ['nullable', 'string', 'max:450'];
        }
        if (in_array($post->type, ['news', 'gallery', 'program', 'facility', 'event', 'achievement'], true)) {
            $rules['cover'] = ['nullable', 'image', 'max:4096'];
            $rules['cover_alt'] = ['nullable', 'string', 'max:250'];
        }
        if (in_array($post->type, ['announcement', 'event'], true)) {
            $rules['attachment'] = ['nullable', 'mimes:pdf', 'max:5120'];
        }
        if ($post->type === 'announcement') {
            $rules['expires_at'] = ['nullable', 'date', 'after:today'];
        }
        if ($post->type === 'news') {
            $rules['is_featured'] = ['nullable', 'boolean'];
        }
        if ($post->type === 'gallery') {
            $rules['gallery_images'] = [$post->gallery_paths ? 'nullable' : 'required', 'array', 'max:12'];
            $rules['gallery_images.*'] = ['image', 'max:4096'];
        }
        if ($post->type === 'event') {
            $rules['event_date'] = ['required', 'date'];
            $rules['event_location'] = ['nullable', 'string', 'max:180'];
        }
        $data = $request->validate($rules);
        if (! $post->exists) {
            $post->slug = Str::slug($data['title']).'-'.Str::lower(Str::random(6));
        }
        foreach (['cover', 'attachment'] as $file) {
            if (array_key_exists($file, $data) && $request->hasFile($file)) {
                $post->{$file.'_path'} = $request->file($file)->storePublicly($file === 'cover' ? 'covers' : 'attachments', 'public');
            }
            unset($data[$file]);
        }
        if ($post->type === 'gallery' && $request->hasFile('gallery_images')) {
            $images = $post->gallery_paths ?? [];
            foreach ($request->file('gallery_images') as $image) {
                $images[] = $image->storePublicly('gallery', 'public');
            }
            $post->gallery_paths = $images;
        }
        unset($data['gallery_images']);
        if ($post->type === 'news') {
            $data['is_featured'] = $request->boolean('is_featured');
        }
        $post->fill($data);
        $post->save();
    }

    private function postList(Request $request, ?string $type = null): View
    {
        return view('manage.posts', [
            'school' => SchoolProfile::current(),
            'posts' => Post::when($type, fn ($query) => $query->where('type', $type))
                ->when($request->user()->role === 'writer', fn ($query) => $query->where('user_id', $request->user()->id))
                ->latest()->paginate(20),
            'type' => $type,
            'contentTypes' => self::CONTENT_TYPES,
        ]);
    }

    private function contentType(string $type): array
    {
        abort_unless(isset(self::CONTENT_TYPES[$type]), 404);

        return self::CONTENT_TYPES[$type];
    }

    private function saveTeacher(Request $request, Teacher $teacher): void
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:180'], 'position' => ['required', 'string', 'max:180'],
            'assignment' => ['nullable', 'string', 'max:180'], 'bio' => ['nullable', 'string', 'max:1500'],
            'photo' => ['nullable', 'image', 'max:2048'], 'sort_order' => ['nullable', 'integer', 'min:0', 'max:9999'],
            'publication_approved' => ['nullable', 'boolean'], 'is_published' => ['nullable', 'boolean'],
        ]);
        if (! $teacher->exists) {
            $teacher->slug = Str::slug($data['name']).'-'.Str::lower(Str::random(6));
        }
        if ($request->hasFile('photo')) {
            $teacher->photo_path = $request->file('photo')->storePublicly('teachers', 'public');
        }
        unset($data['photo']);
        $data['publication_approved'] = $request->boolean('publication_approved');
        $data['is_published'] = $request->boolean('is_published') && $data['publication_approved'];
        $teacher->fill($data);
        $teacher->save();
    }

    private function authorizePost(Request $request, Post $post): void
    {
        abort_if($request->user()->role === 'writer' && $post->user_id !== $request->user()->id, 403);
    }

    private function adminOnly(): void
    {
        abort_unless(auth()->user()?->role === 'admin', 403);
    }
}
