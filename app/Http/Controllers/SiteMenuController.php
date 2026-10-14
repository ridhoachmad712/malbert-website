<?php

namespace App\Http\Controllers;

use App\Models\SchoolProfile;
use App\Models\SiteMenuItem;
use App\Models\SitePage;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class SiteMenuController extends Controller
{
    public function index(): View
    {
        $this->adminOnly();

        return view('manage.menu', [
            'school' => SchoolProfile::current(),
            'items' => SiteMenuItem::with('page')->orderByRaw("CASE location WHEN 'primary' THEN 0 WHEN 'more' THEN 1 ELSE 2 END")->orderBy('sort_order')->get(),
        ]);
    }

    public function create(): View
    {
        $this->adminOnly();

        return $this->form(new SiteMenuItem);
    }

    public function edit(SiteMenuItem $siteMenuItem): View
    {
        $this->adminOnly();

        return $this->form($siteMenuItem);
    }

    public function store(Request $request): RedirectResponse
    {
        $this->adminOnly();
        $data = $this->validatedData($request);
        $this->validateCapacity($data);
        $item = SiteMenuItem::create($data);

        return redirect()->route('manage.menu.edit', $item)->with('success', 'Menu dibuat.');
    }

    public function update(Request $request, SiteMenuItem $siteMenuItem): RedirectResponse
    {
        $this->adminOnly();
        $data = $this->validatedData($request);
        $this->validateCapacity($data, $siteMenuItem);
        $siteMenuItem->update($data);

        return back()->with('success', 'Menu diperbarui.');
    }

    public function destroy(SiteMenuItem $siteMenuItem): RedirectResponse
    {
        $this->adminOnly();
        $siteMenuItem->delete();

        return redirect()->route('manage.menu')->with('success', 'Menu dihapus.');
    }

    private function form(SiteMenuItem $item): View
    {
        return view('manage.menu-form', [
            'school' => SchoolProfile::current(),
            'item' => $item,
            'pages' => SitePage::orderBy('title')->get(),
        ]);
    }

    private function validatedData(Request $request): array
    {
        $data = $request->validate([
            'label' => ['required', 'string', 'max:40'],
            'target_type' => ['required', Rule::in(['route', 'page', 'external'])],
            'target_route' => ['nullable', 'required_if:target_type,route', Rule::in(array_keys(SiteMenuItem::ROUTES))],
            'site_page_id' => ['nullable', 'required_if:target_type,page', Rule::exists('site_pages', 'id')->where('is_published', true)],
            'external_url' => ['nullable', 'required_if:target_type,external', 'url:https', 'max:500'],
            'location' => ['required', Rule::in(['primary', 'more', 'cta'])],
            'sort_order' => ['required', 'integer', 'min:0', 'max:999'],
            'is_enabled' => ['required', 'boolean'],
            'show_in_footer' => ['required', 'boolean'],
            'open_new_tab' => ['required', 'boolean'],
        ]);
        if ($data['target_type'] !== 'route') {
            $data['target_route'] = null;
        }
        if ($data['target_type'] !== 'page') {
            $data['site_page_id'] = null;
        }
        if ($data['target_type'] !== 'external') {
            $data['external_url'] = null;
        }

        return $data;
    }

    private function validateCapacity(array $data, ?SiteMenuItem $item = null): void
    {
        if (! $data['is_enabled']) {
            return;
        }
        $limit = $data['location'] === 'primary' ? 5 : ($data['location'] === 'cta' ? 1 : null);
        if ($limit === null) {
            return;
        }
        $count = SiteMenuItem::where('is_enabled', true)->where('location', $data['location'])
            ->when($item, fn ($query) => $query->where('id', '!=', $item->id))->count();
        if ($count >= $limit) {
            throw ValidationException::withMessages(['location' => 'Tempat ini sudah penuh. Pindahkan atau nonaktifkan menu lain terlebih dahulu.']);
        }
    }

    private function adminOnly(): void
    {
        abort_unless(auth()->user()?->role === 'admin', 403);
    }
}
