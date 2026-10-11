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
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class ManageController extends Controller
{
    public function dashboard(Request $request): View
    {
        return view('manage.dashboard', ['school' => SchoolProfile::current(), 'drafts' => Post::whereIn('status', ['draft', 'review'])->when($request->user()->role === 'writer', fn ($q) => $q->where('user_id', $request->user()->id))->latest()->take(8)->get(), 'postCount' => Post::count(), 'teacherCount' => Teacher::where('is_published', true)->count()]);
    }

    public function posts(Request $request): View
    {
        return view('manage.posts', ['school' => SchoolProfile::current(), 'posts' => Post::when($request->user()->role === 'writer', fn ($q) => $q->where('user_id', $request->user()->id))->latest()->paginate(20)]);
    }

    public function newPost(): View
    {
        return view('manage.post-form', ['school' => SchoolProfile::current(), 'post' => new Post]);
    }

    public function editPost(Request $request, Post $post): View
    {
        $this->authorizePost($request, $post);

        return view('manage.post-form', ['school' => SchoolProfile::current(), 'post' => $post]);
    }

    public function previewPost(Request $request, Post $post): View
    {
        $this->authorizePost($request, $post);

        return view('site.post', ['school' => SchoolProfile::current(), 'post' => $post, 'type' => ['news' => 'berita', 'announcement' => 'pengumuman', 'gallery' => 'galeri', 'program' => 'program', 'facility' => 'fasilitas', 'event' => 'agenda', 'achievement' => 'prestasi', 'faq' => 'faq'][$post->type], 'preview' => true]);
    }

    public function storePost(Request $request): RedirectResponse
    {
        $post = new Post(['user_id' => $request->user()->id]);
        $this->savePost($request, $post);

        return redirect()->route('manage.posts')->with('success', 'Draf konten tersimpan.');
    }

    public function updatePost(Request $request, Post $post): RedirectResponse
    {
        $this->authorizePost($request, $post);
        abort_if($request->user()->role === 'writer' && $post->status === 'published', 403);
        $this->savePost($request, $post);

        return redirect()->route('manage.posts')->with('success', 'Konten diperbarui.');
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
            'admission_intro' => ['nullable', 'string', 'max:3000'], 'admission_requirements' => ['nullable', 'string', 'max:5000'],
            'admission_steps' => ['nullable', 'string', 'max:5000'], 'admission_schedule' => ['nullable', 'string', 'max:3000'],
            'admission_url' => ['nullable', 'url:http,https', 'max:500'],
            'logo' => ['nullable', 'image', 'max:2048'], 'hero' => ['nullable', 'image', 'max:4096'],
        ]);
        foreach (['logo', 'hero'] as $image) {
            if ($request->hasFile($image)) {
                $data[$image.'_path'] = $request->file($image)->storePublicly('school', 'public');
            }
            unset($data[$image]);
        }
        SchoolProfile::current()->update($data);

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
        $data = $request->validate([
            'type' => ['required', Rule::in(['news', 'announcement', 'gallery', 'program', 'facility', 'event', 'achievement', 'faq'])],
            'title' => ['required', 'string', 'max:180'], 'summary' => ['nullable', 'string', 'max:450'],
            'body' => ['required', 'string', 'max:30000'], 'cover' => ['nullable', 'image', 'max:4096'],
            'cover_alt' => ['nullable', 'string', 'max:250'], 'attachment' => ['nullable', 'mimes:pdf', 'max:5120'],
            'expires_at' => ['nullable', 'date', 'after:today'], 'is_featured' => ['nullable', 'boolean'],
            'gallery_images' => ['nullable', 'array', 'max:12'], 'gallery_images.*' => ['image', 'max:4096'],
            'event_date' => ['required_if:type,event', 'nullable', 'date'], 'event_location' => ['nullable', 'string', 'max:180'],
        ]);
        if (! $post->exists) {
            $post->slug = Str::slug($data['title']).'-'.Str::lower(Str::random(6));
        }
        foreach (['cover', 'attachment'] as $file) {
            if ($request->hasFile($file)) {
                $post->{$file.'_path'} = $request->file($file)->storePublicly($file === 'cover' ? 'covers' : 'attachments', 'public');
            }
            unset($data[$file]);
        }
        if ($request->hasFile('gallery_images')) {
            $images = $post->gallery_paths ?? [];
            foreach ($request->file('gallery_images') as $image) {
                $images[] = $image->storePublicly('gallery', 'public');
            }
            $post->gallery_paths = $images;
        }
        unset($data['gallery_images']);
        $data['is_featured'] = $request->boolean('is_featured') && $data['type'] === 'news';
        $post->fill($data);
        $post->save();
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
