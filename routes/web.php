<?php

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
// les routes d'authentification (Se connecter, S'inscrire ...)
Auth::routes();
// Les routes publiques
// Page d'accueil
Route::get('/home', 'HomeController@index')->name('home');
Route::get('/', 'HomeController@index');



// Les routes protégées
Route::group(['middleware' => ['auth']], function() {
    
    //Praticien Controller
    //Récupération d'un praticien pour alimenter la page consultePraticien
    Route::get('/consulterPraticien/{id}', 'PraticienController@getPraticien')->middleware('can:admin');
    Route::post('/listerPraticiensNom', 'PraticienController@showPraticiens')->middleware('can:admin');
    //Lister tous les praticiens d'une spécialité
    Route::post('/listerPraticiensSpecialite', 'PraticienController@getPraticiensSpecialite')->middleware('can:admin');
    Route::get('/listerPraticiens/{erreur?}', 'PraticienController@getPraticiens')->middleware('can:admin');
    
    //SpecialiteController
    //Route pour rechercher Praticiens par spécialités
    Route::get('/listerSpecialites/{erreur?}', 'SpecialiteController@getSpecialites')->middleware('can:admin');

    //Route pour accéder au formulaire de modification/ajout des spécialités
    Route::get('/modifierSpecialite/{id_praticien}/{id_specialite}','SpecialiteController@showPraticienSpecialite')->middleware('can:admin');
    Route::get('/ajouterSpecialite/{id_praticien}/{id_specialite}', 'SpecialiteController@showPraticienSpecialite')->middleware('can:admin');
    Route::post('/validerSpecialite/', 'SpecialiteController@validateSpecialite')->middleware('can:admin');

    //Route de suppression d'une spécialité
    Route::get('/supprimerSpecialite/{id_praticien}/{id_specialite}', 'SpecialiteController@deleteSpecialite')->middleware('can:admin');

});
