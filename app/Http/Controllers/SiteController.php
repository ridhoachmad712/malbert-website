<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\SchoolProfile;
use App\Models\Teacher;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class SiteController extends Controller
{
    public function home(): View
    {
        return view('site.home', [
            'school' => SchoolProfile::current(),
            'announcements' => Post::public()->where('type', 'announcement')->latest('published_at')->take(2)->get(),
            'featured' => Post::public()->where('type', 'news')->where('is_featured', true)->latest('published_at')->first(),
            'news' => Post::public()->where('type', 'news')->latest('published_at')->take(3)->get(),
            'events' => Post::public()->where('type', 'event')->whereDate('event_date', '>=', today())->orderBy('event_date')->take(3)->get(),
            'programs' => Post::public()->where('type', 'program')->latest('published_at')->take(3)->get(),
            'achievements' => Post::public()->where('type', 'achievement')->latest('published_at')->take(2)->get(),
            'teachers' => Teacher::where('is_published', true)->where('publication_approved', true)->orderBy('sort_order')->take(4)->get(),
        ]);
    }

    public function profile(): View
    {
        return view('site.profile', ['school' => SchoolProfile::current()]);
    }

    public function teachers(Request $request): View
    {
        $query = Teacher::where('is_published', true)->where('publication_approved', true);

        if ($request->filled('q')) {
            $query->where('name', 'like', '%'.$request->string('q')->trim().'%');
        }

        return view('site.teachers', ['school' => SchoolProfile::current(), 'teachers' => $query->orderBy('sort_order')->orderBy('name')->paginate(12)->withQueryString()]);
    }

    public function teacher(Teacher $teacher): View
    {
        abort_unless($teacher->is_published && $teacher->publication_approved, 404);

        return view('site.teacher', ['school' => SchoolProfile::current(), 'teacher' => $teacher]);
    }

    public function news(): View
    {
        return $this->listing('news', 'Berita sekolah');
    }

    public function announcements(): View
    {
        return $this->listing('announcement', 'Pengumuman');
    }

    public function gallery(): View
    {
        return $this->listing('gallery', 'Galeri kegiatan');
    }

    public function programs(): View
    {
        return $this->listing('program', 'Program sekolah');
    }

    public function facilities(): View
    {
        return $this->listing('facility', 'Fasilitas sekolah');
    }

    public function events(): View
    {
        return $this->listing('event', 'Agenda sekolah');
    }

    public function achievements(): View
    {
        return $this->listing('achievement', 'Prestasi');
    }

    public function admissions(): View
    {
        return view('site.admissions', ['school' => SchoolProfile::current()]);
    }

    public function faq(): View
    {
        return view('site.faq', ['school' => SchoolProfile::current(), 'questions' => Post::public()->where('type', 'faq')->orderBy('title')->get()]);
    }

    public function post(string $type, Post $post): View
    {
        $types = ['berita' => 'news', 'pengumuman' => 'announcement', 'galeri' => 'gallery', 'program' => 'program', 'fasilitas' => 'facility', 'agenda' => 'event', 'prestasi' => 'achievement'];
        abort_unless($post->type === $types[$type] && $post->status === 'published' && $post->published_at?->isPast() && (! $post->expires_at || $post->expires_at->isFuture()), 404);

        return view('site.post', ['school' => SchoolProfile::current(), 'post' => $post, 'type' => $type]);
    }

    public function contact(): View
    {
        return view('site.contact', ['school' => SchoolProfile::current()]);
    }

    public function sitemap(): Response
    {
        $urls = [route('home'), route('profile'), route('teachers'), route('news'), route('announcements'), route('gallery'), route('programs'), route('facilities'), route('events'), route('achievements'), route('admissions'), route('faq'), route('contact')];
        foreach (Post::public()->where('type', '!=', 'faq')->get() as $post) {
            $urls[] = $post->publicUrl();
        }
        foreach (Teacher::where('is_published', true)->where('publication_approved', true)->get() as $teacher) {
            $urls[] = route('teacher', $teacher->slug);
        }
        $entries = implode('', array_map(fn (string $url): string => '<url><loc>'.htmlspecialchars($url, ENT_XML1).'</loc></url>', $urls));

        return response('<?xml version="1.0" encoding="UTF-8"?><urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'.$entries.'</urlset>')->header('Content-Type', 'application/xml');
    }

    public function robots(): Response
    {
        return response("User-agent: *\nAllow: /\nDisallow: /kelola\nDisallow: /masuk\nSitemap: ".route('sitemap')."\n")->header('Content-Type', 'text/plain');
    }

    private function listing(string $type, string $title): View
    {
        return view('site.listing', [
            'school' => SchoolProfile::current(),
            'posts' => Post::public()->where('type', $type)->when($type === 'event', fn ($query) => $query->orderBy('event_date'))->when($type !== 'event', fn ($query) => $query->latest('published_at'))->paginate(9),
            'title' => $title,
            'type' => $type,
        ]);
    }
}
