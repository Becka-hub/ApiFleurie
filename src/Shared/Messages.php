<?php
namespace App\Shared;

class Messages 
{
    public const SUCCESS  = ['status' => true, 'title' => 'success','message'=>'success','code' => 200];
    public const SUCCESS_INSERT  = ['status' => true, 'title' => 'success','message'=>'Insertion avec success', 'code' => 200];
    public const SUCCESS_UPDATE  = ['status' => true, 'title' => 'success','message'=>'Modification avec success', 'code' => 200];
    public const SUCCESS_DELETE  = ['status' => true, 'title' => 'success','message'=>'Supression avec success', 'code' => 200];
    public const REGISTER_SUCCESS  = ['status' => true, 'title' => 'success','message'=>'Inscription avec success', 'code' => 200];
    public const ACHAT_SUCCESS  = ['status' => true, 'title' => 'success','message'=>'Merci pour votre achat', 'code' => 200];
    public const CHECK_EMAIL = ['status' => true, 'title' => 'success','message'=>'Veuillez verifier votre email', 'code' => 200];
    public const SUCCESS_RESETPASSWORD = ['status' => true, 'title' => 'success','message'=>'Mot de passe a bien changer', 'code' => 200];


    public const ERROR  = ['status' => false, 'title' => 'error','message'=>'error','code' => 400];
    public const FORM_INVALID= ['status' => false,'title'=>'error','message'=>'Quelque champ du formulaire est vide','code'=>400];
    public const FLEUR_NOT_FOUND= ['status' => false,'title'=>'error','message'=>'Fleurs introuvable','code'=>404];
    public const FLEURIE_EXIST= ['status' => false,'title'=>'error','message'=>'Fleurie déjà existé','code'=>400];
    public const CATEGORIE_NOT_FOUND= ['status' => false,'title'=>'error','message'=>'Categories introuvable','code'=>404];
    public const CATEGORIE_EXIST= ['status' => false,'title'=>'error','message'=>'Categories déjà existé','code'=>400];
    public const USER_NOT_FOUND= ['status' => false,'title'=>'error','message'=>'Utilisateur introuvable','code'=>404];
    public const MAILUSED = ['status'=>false,'title' => 'error', 'message' => 'Email déjà utilisé', 'code' => 400];
    public const PASSWORD_WRONG= ['status' => false,'title'=>'error','message'=>'Mot de passe incorrect','code'=>400];
    public const TOKEN_INVALID= ['status' => false,'title'=>'error','message'=>'Token invalid','code'=>400];
    public const BAD_URL_TOKEN= ['status' => false,'title'=>'error','message'=>'Cette url ne contient pas de token','code'=>400];
    
}