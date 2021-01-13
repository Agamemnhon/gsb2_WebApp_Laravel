<?php

namespace App\Http\Controllers;
use App\Models\Praticien;
use App\Models\Specialite;
//use Illuminate\Http\Request;
use Session;
use Request;

class PraticienController extends Controller
{
    /**
     * Récupère la liste de tous les praticiens et renvoi
     * cette liste $praticiens à la vue 'listePraticien'
     * @return type
     */
    public function getPraticien($id){
        $praticien = Praticien::find($id);
        $titre = "Consultation de Praticien";
        $tablePivot = $praticien->specialites;
        return view('consultePraticien', compact('praticien','tablePivot', 'titre'));
    }
    
    /**
     * Fonction qui récupère tous les praticiens et réoriente vers le formulaire
     * de saisie de nom
     * Objectif: Evolution pour saisie automatique TODO: Upgrade le formulaire de saisie du nom
     * @param type $erreur
     * @return type
     */
    public function getPraticiens( $erreur=""){
        $titre="Recherche des Praticiens";
        $praticiens= Praticien::all();
        return view('formPraticien', compact('praticiens','titre','erreur'));
    }

    public function showPraticiens(){
        $erreur = Session::get('erreur');
        Session::forget('erreur');
        $titre = "Liste des praticien(s)";
        $nom_praticien=Request::input('nom');
        if($nom_praticien){
            $praticiens=Praticien::where('nom_praticien',$nom_praticien)->get();
            if($praticiens->first()){
                return view('listePraticiens', compact('praticiens','titre'));
            } else {
                $erreur = "Ce nom n'existe pas !";
                Session::put(['erreur' => $erreur]);
            return redirect('/listerPraticiens/')
            ->with('error', 'Il n\'existe aucun praticien portant ce nom !');
            }
        } else {
            return redirect('/listerPraticiens/')
                ->with('error', 'il faut saisir un nom !');
        }
    }
    /**
     * Fonction qui récupère les praticiens possédant 
     * la spécialité dont l'id vaut 'cbSpecialite'
     * 
     * @return s'il trouve, renvoie la view 'listePraticiens' avec une collection
     * 'praticiens'
     */
    public function getPraticiensSpecialite() {
        $erreur = Session::get('erreur');
        Session::forget('erreur');
        $titre = "Consultation de Praticien";
        $id_specialite = Request::input('cbSpecialite');
        if ($id_specialite){
            $specialite = Specialite::find($id_specialite);
            $tablePivot = $specialite->praticiens;
            if(!$tablePivot->first()){
                return redirect('/listerSpecialites')->with('error', 'Il n\'y a aucun praticien ayant cette spécialité !');
            }
            $idPraticiens=[];
            foreach($tablePivot as $pivot){
                $idPraticiens[]=$pivot->pivot->id_praticien ;
            };
            $praticiens = Praticien::all()->intersect(Praticien::whereIn('id_praticien', $idPraticiens)->get());
            return view('listePraticiens', compact('praticiens', 'titre'));
        } else {
            $erreur = "Il faut sélectionner une spécialité !";
            return redirect('/listerSpecialites/'.$erreur);
        }
    }
}
