<?php

use App\Http\Controllers\LoginController;
use App\Http\Controllers\ManageController;
use App\Http\Controllers\SiteController;
use Illuminate\Support\Facades\Route;

Route::get('/', [SiteController::class, 'home'])->name('home');
Route::get('/profil', [SiteController::class, 'profile'])->name('profile');
Route::get('/guru', [SiteController::class, 'teachers'])->name('teachers');
Route::get('/guru/{teacher:slug}', [SiteController::class, 'teacher'])->name('teacher');
Route::get('/berita', [SiteController::class, 'news'])->name('news');
Route::get('/pengumuman', [SiteController::class, 'announcements'])->name('announcements');
Route::get('/galeri', [SiteController::class, 'gallery'])->name('gallery');
Route::get('/program', [SiteController::class, 'programs'])->name('programs');
Route::get('/fasilitas', [SiteController::class, 'facilities'])->name('facilities');
Route::get('/agenda', [SiteController::class, 'events'])->name('events');
Route::get('/prestasi', [SiteController::class, 'achievements'])->name('achievements');
Route::get('/penerimaan', [SiteController::class, 'admissions'])->name('admissions');
Route::get('/faq', [SiteController::class, 'faq'])->name('faq');
Route::get('/{type}/{post:slug}', [SiteController::class, 'post'])->whereIn('type', ['berita', 'pengumuman', 'galeri', 'program', 'fasilitas', 'agenda', 'prestasi'])->name('post');
Route::get('/kontak', [SiteController::class, 'contact'])->name('contact');
Route::get('/sitemap.xml', [SiteController::class, 'sitemap'])->name('sitemap');
Route::get('/robots.txt', [SiteController::class, 'robots'])->name('robots');

Route::get('/masuk', [LoginController::class, 'form'])->middleware('guest')->name('login');
Route::post('/masuk', [LoginController::class, 'login'])->middleware(['guest', 'throttle:5,1'])->name('login.attempt');
Route::post('/keluar', [LoginController::class, 'logout'])->middleware('auth')->name('logout');

Route::middleware('auth')->prefix('kelola')->name('manage.')->group(function (): void {
    Route::get('/', [ManageController::class, 'dashboard'])->name('dashboard');
    Route::get('/konten', [ManageController::class, 'posts'])->name('posts');
    Route::get('/konten/baru', [ManageController::class, 'newPost'])->name('posts.create');
    Route::post('/konten', [ManageController::class, 'storePost'])->name('posts.store');
    Route::get('/konten/{post}/ubah', [ManageController::class, 'editPost'])->name('posts.edit');
    Route::get('/konten/{post}/pratinjau', [ManageController::class, 'previewPost'])->name('posts.preview');
    Route::put('/konten/{post}', [ManageController::class, 'updatePost'])->name('posts.update');
    Route::post('/konten/{post}/status', [ManageController::class, 'postStatus'])->name('posts.status');
    Route::get('/guru', [ManageController::class, 'teachers'])->name('teachers');
    Route::get('/guru/baru', [ManageController::class, 'newTeacher'])->name('teachers.create');
    Route::post('/guru', [ManageController::class, 'storeTeacher'])->name('teachers.store');
    Route::get('/guru/{teacher}/ubah', [ManageController::class, 'editTeacher'])->name('teachers.edit');
    Route::put('/guru/{teacher}', [ManageController::class, 'updateTeacher'])->name('teachers.update');
    Route::get('/profil', [ManageController::class, 'editProfile'])->name('profile');
    Route::put('/profil', [ManageController::class, 'updateProfile'])->name('profile.update');
    Route::get('/pengguna', [ManageController::class, 'users'])->name('users');
    Route::post('/pengguna', [ManageController::class, 'storeUser'])->name('users.store');
    Route::get('/akun', [ManageController::class, 'account'])->name('account');
    Route::put('/akun', [ManageController::class, 'updatePassword'])->name('account.update');
});
