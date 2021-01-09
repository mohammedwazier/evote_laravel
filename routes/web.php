<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::group(['middleware' => 'logdata'], function(){
    Route::group(['middleware' => 'indexauth'], function(){
        Route::get('/', 'HomepageController@index')->name('homepage.login');
        Route::get('/login', 'HomepageController@index')->name('homepage.login.page');
        Route::post('/login', 'HomepageController@loginProcess')->name('homepage.login.process');
        Route::get('/register', 'HomepageController@register')->name('homepage.register');
        Route::post('/register', 'HomepageController@registerProcess')->name('homepage.register.process');
        Route::get('/verification/{key}', 'HomepageController@verification')->name('homepage.verification');
    });

    Route::group(['middleware' => 'logauth'], function(){
        Route::prefix('dashboard')->group(function(){
            // Kalau Admin diarahin ke list election yang telah dibuat
            Route::get('/', 'DashboardController@index')->name('dashboard.index');

            Route::group(['middleware' => 'authmanager'], function(){
                Route::get('/create-election', 'DashboardController@createElection')->name('dashboard.election.create');
                Route::post('/create-election', 'DashboardController@storeElection')->name('dashboard.election.store');
                Route::get('/election/edit/{id}', 'DashboardController@editElection')->name('dashboard.election.edit');
                Route::post('/election/edit/{id}', 'DashboardController@updateElection')->name('dashboard.election.update');
                Route::get('/election/delete/{id}', 'DashboardController@deleteElection')->name('dashboard.election.delete');
                Route::get('/election/close/{id}', 'DashboardController@closeElection')->name('dashboard.election.close');
                Route::get('/election/start/{id}', 'DashboardController@startElection')->name('dashboard.election.start');
            });
        });
    });

    Route::get('/logout', 'HomepageController@logout')->name('dashboard.logout');
});

Route::get('/election/vote/{id}', 'DashboardController@viewVote')->name('dashboard.vote.view');
Route::get('/election/vote/{id}/{key}', 'DashboardController@voteVoted')->name('dashboard.vote.vote');
Route::post('/election/vote/{id}/{key}/{blt_key}', 'DashboardController@voteVotedStore')->name('dashboard.vote.voted');
Route::get('/election/ballots/{id}', 'DashboardController@viewBallot')->name('ballot.view');
Route::get('/election/ballot/{id}/detail/{ballot_uid}', 'DashboardController@detailBallot')->name('ballot.details');
Route::get('/election/results/{id}', 'DashboardController@electionResult')->name('election.result');

