<?php

namespace App\Http\Controllers;
use Session;
use Request;
use App\Models\Specialite;
use App\Models\Praticien;
use Validator;

class SpecialiteController extends Controller
{
    /**
     * Fonction qui récupère toutes les spécialités et qui 
     * alimente la liste du menu déroulant
     * @param type $erreur
     * @return vers le formulaire 'fomSelectSpecialite' avec une collection de ttes les 
     * spécialités
     */
    public function getSpecialites($erreur = ""){
        $specialites = Specialite::all();
        return view('formSelectSpecialite', compact('specialites', 'erreur'));
    }
    
    /**
     * Fonction qui affiche tous les praticiens
     * possédant une spécialité
     * @param type $id_praticien
     * @param type $id_specialite
     * @return la view 'formSpecialite' avec les collections praticiens et specialites
     */
    public function showPraticienSpecialite($id_praticien, $id_specialite){
        $praticien = Praticien::find($id_praticien);
        $specialites = Specialite::all();
        $titre = "Modification d'une spécialité";
        $erreur = "";
        $pivot = $praticien->specialites()->find($id_specialite);
        if ($id_specialite>0){
            $readonly = true;
        } else {
            $readonly = false;
            $titre = "Ajouter une spécialité";
        }
        return view('formSpecialite', compact('praticien', 'specialites', 'erreur', 'titre','pivot','erreur', 'readonly'));
    }
    
    /**
    * Fonction qui gère la validation des données du formulaire FormSpecialites
    * Applique le validator pour vérification
    * Revient à la page de consultation d'un praticien et de ses spécialités
    **/
    public function validateSpecialite() {
        // Récupération des valeurs saisies
        $id_praticien = Request::input('id_praticien'); // id dans le champs caché
        $id_specialite = Request::input('cbSpecialite'); // Liste déroulante des spécialites
        $erreur = Session::get('erreur');
        Session::forget('erreur');
        
        // Liste des champs à vérifier
        $regles = array(
            'cbSpecialite' => 'required',
            'diplome' => 'required',
            'coef_prescription' => 'required|numeric',
        );
        
        // Messages d'erreur personnalisés
        $messages = array(
            'cbSpecialite.required' => 'Il faut sélectionner une specialite.',
            'diplome.required' => 'Il faut saisir un diplome.',
            'coef_prescription.required' => 'Il faut saisir un coef. de prescription.',
            'coef_prescription.numeric' => 'Coef. Prescription doit être une valeur numérique.'
        );
        $validator = Validator::make(Request::all(), $regles, $messages);
        
        // On retourne au formulaire s'il y a un problème
        if ($validator->fails()) {
            if ($id_specialite > 0) {
                return redirect('modifierSpecialite/' . $id_praticien.'/'.$id_specialite)
                                ->withErrors($validator)
                                ->withInput();
            } else {
                return redirect('ajouterSpecialite/'. $id_praticien.'/0') 
                                ->withErrors($validator)
                                ->withInput();
            }
        }

        // Si Validator OK: on récupère les valeurs et on les sauvegarde
        $diplome = Request::input('diplome');
        $coef_prescription = Request::input('coef_prescription');
        $praticien = Praticien::find($id_praticien);
        
        try {

            $praticien->specialites()->detach($id_specialite);
            $praticien->specialites()->attach($id_specialite, ['diplome'=>$diplome, 'coef_prescription'=>$coef_prescription]);
        } catch (Exception $ex) {
            $erreur = $ex->getMessage();
            Session::put('erreur', $erreur);
            return redirect('/modifierSpecialite/' . $id_praticien.'/'.$id_specialite);
        }
        // On réaffiche la liste des Specialites du praticien
        return redirect('/consulterPraticien/'.$id_praticien);
    }
    /**
     * Fonction qui efface une specialité à un praticien
     * @param type $id_praticien
     * @param type $id_specialite
     * @return type
     */
    public function deleteSpecialite($id_praticien, $id_specialite){
        $praticien = Praticien::find($id_praticien);
        $praticien->specialites()->detach($id_specialite);
        $titre = "Consultation de Praticien";
        $tablePivot = $praticien->specialites;
        return view('consultePraticien', compact('praticien','tablePivot', 'titre'));

    }
}
