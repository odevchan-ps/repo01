<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CodeListController;
use App\Http\Controllers\NewsArticleController;
use App\Http\Controllers\NewsArticlesController;
use App\Http\Controllers\XPostController;
use App\Http\Controllers\XVectorController;
use App\Http\Controllers\XPromptController;
use App\Http\Controllers\XGeneratedPostController;
use App\Http\Controllers\XReplyController;
use App\Http\Controllers\XFeedbackController;
use App\Http\Controllers\NewsImportController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Route::get('/', function () {
//     return view('welcome');
// });
// Route::get('/', function () {
//     return redirect()->route('news_articles.index'); // 初期表示ページを指定
// });
// トップ画面（ホームページ）をニュース管理画面に設定
Route::get('/', [NewsArticlesController::class, 'index'])->name('home');

Route::resource('news_articles', NewsArticleController::class);
Route::resource('x_posts', XPostController::class);
Route::resource('x_vectors', XVectorController::class);
Route::resource('x_prompts', XPromptController::class);
Route::resource('x_generated_posts', XGeneratedPostController::class);
Route::resource('x_replies', XReplyController::class);
Route::resource('x_feedbacks', XFeedbackController::class);
Route::resource('code_list', CodeListController::class);

Route::get('/news-management', [NewsArticlesController::class, 'index'])->name('news_articles.management');
Route::post('/news-management/import', [NewsArticlesController::class, 'importNews'])->name('news.import');