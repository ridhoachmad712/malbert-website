<?php

namespace App\Http\Controllers;

use App\Models\SchoolProfile;
use App\Models\SitePage;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class SitePageController extends Controller
{
    public function show(SitePage $sitePage): View
    {
        abort_unless($sitePage->is_published, 404);

        return view('site.custom-page', ['school' => SchoolProfile::current(), 'page' => $sitePage]);
    }

    public function index(): View
    {
        $this->adminOnly();

        return view('manage.pages', ['school' => SchoolProfile::current(), 'pages' => SitePage::latest('updated_at')->get()]);
    }

    public function create(): View
    {
        $this->adminOnly();

        return view('manage.page-form', ['school' => SchoolProfile::current(), 'page' => new SitePage]);
    }

    public function edit(SitePage $sitePage): View
    {
        $this->adminOnly();

        return view('manage.page-form', ['school' => SchoolProfile::current(), 'page' => $sitePage]);
    }

    public function preview(SitePage $sitePage): View
    {
        $this->adminOnly();

        return view('site.custom-page', ['school' => SchoolProfile::current(), 'page' => $sitePage, 'preview' => true]);
    }

    public function store(Request $request): RedirectResponse
    {
        $this->adminOnly();
        $page = SitePage::create($this->validatedData($request));

        return redirect()->route('manage.pages.edit', $page)->with('success', 'Halaman dibuat. Terbitkan setelah isinya diperiksa.');
    }

    public function update(Request $request, SitePage $sitePage): RedirectResponse
    {
        $this->adminOnly();
        $sitePage->update($this->validatedData($request, $sitePage));

        return back()->with('success', 'Halaman diperbarui.');
    }

    public function destroy(SitePage $sitePage): RedirectResponse
    {
        $this->adminOnly();
        $sitePage->delete();

        return redirect()->route('manage.pages')->with('success', 'Halaman dihapus. Menu yang menujunya kini disembunyikan.');
    }

    private function validatedData(Request $request, ?SitePage $page = null): array
    {
        $request->merge(['slug' => Str::slug((string) $request->input('slug'))]);
        $data = $request->validate([
            'title' => ['required', 'string', 'max:180'],
            'slug' => ['required', 'alpha_dash:ascii', 'max:180', Rule::unique('site_pages', 'slug')->ignore($page?->id)],
            'intro' => ['nullable', 'string', 'max:500'],
            'body' => ['nullable', 'string', 'max:50000'],
            'image' => ['nullable', 'image', 'mimes:png,jpg,jpeg,webp', 'max:6144'],
            'layout' => ['required', Rule::in(['standard', 'wide', 'split'])],
            'seo_description' => ['nullable', 'string', 'max:160'],
            'is_published' => ['required', 'boolean'],
        ]);
        if ($request->hasFile('image')) {
            $data['image_path'] = $request->file('image')->storePublicly('pages', 'public');
        }
        unset($data['image']);

        return $data;
    }

    private function adminOnly(): void
    {
        abort_unless(auth()->user()?->role === 'admin', 403);
    }
}
