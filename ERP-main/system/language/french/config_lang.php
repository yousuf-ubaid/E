<?php
/**
 * System messages translation for CodeIgniter(tm)
 *
 * @prefix: config_
 */
/*common*/
$lang['config_step_one'] = 'Étape 1 ';
$lang['config_step_two'] = 'Étape 2 ';
$lang['config_step_three'] = 'Étape 3 ';
$lang['config_gl_system_code'] = 'Code du système GL';
$lang['config_gl_description'] = 'Description GL';
$lang['config_document_id'] = 'ID du document';
$lang['config_not_active'] = 'Non actif';
$lang['config_create_approval_user'] = 'Créer un utilisateur d&apos;approbation';
$lang['config_you_want_to_update_this'] = 'Vous voulez mettre à jour ceci !';
$lang['config_you_want_to_update_this_record'] = 'Vous voulez mettre à jour cet enregistrement !';
$lang['config_you_want_to_reverse_this_record'] = 'Vous voulez inverser ce document !';
/*Template Configuration*/
$lang['config_template_setup'] = 'Configuration du modèle';
$lang['config_default_value'] = 'Valeur par défaut';
/*Company Configuration*/
$lang['config_company_configuration'] = 'Configuration de l&apos;entreprise';
$lang['config_logo'] = 'Logo';
$lang['config_company_details'] = 'Détails de l&apos;entreprise';
$lang['config_add_company'] = 'Ajouter une entreprise';
$lang['config_company_header'] = 'En-tête de l&apos;entreprise';
$lang['config_company_control_accounts'] = 'Comptes de contrôle de l&apos;entreprise';
$lang['config_company_code'] = 'Code de l&apos;entreprise';
$lang['config_company_name'] = 'Nom de l&apos;entreprise';
$lang['config_company_url'] = 'URL de l&apos;entreprise';
$lang['config_company_email'] = 'Email de l&apos;entreprise';
$lang['config_company_phone'] = 'Téléphone de l&apos;entreprise';
$lang['config_company_start_date'] = 'Date de début de l&apos;entreprise';
$lang['config_company_default_currency'] = 'Devise par défaut de l&apos;entreprise';
$lang['config_company_reporting_currency'] = 'Devise de reporting de l&apos;entreprise';
$lang['config_company_legal_name'] = 'Nom juridique';
$lang['config_tax_identification_no'] = 'Numéro d&apos;identification fiscale';
$lang['config_tax_year'] = 'Année fiscale';
$lang['config_industry'] = 'Secteur';
$lang['config_default_segment'] = 'Segment par défaut';
$lang['config_permenet'] = 'Permanent';
$lang['config_control_account'] = 'Compte de contrôle';
$lang['config_gl_account'] = 'Compte GL';
$lang['config_document_code'] = 'Code du document';
$lang['config_control_account_description'] = 'Description du compte de contrôle';
$lang['config_control_accounts'] = 'Comptes de contrôle';
$lang['config_account_type'] = 'Type de compte';
$lang['config_master_account'] = 'Compte principal';
$lang['config_select_status'] = 'Sélectionner le statut';

$secondary_code = getPolicyValues('SCAC', 'All');

if($secondary_code == 1){
    $lang['config_secondary_code'] = 'Code de compte';
}else{
    $lang['config_secondary_code'] = 'Code secondaire';
}

$lang['config_account_name'] = 'Nom du compte';
$lang['config_crop_image'] = 'Recadrer l&apos;image';
$lang['config_crop'] = 'Recadrer l&apos;image';
$lang['config_company_code_is_required'] = 'Le code de l&apos;entreprise est requis';
$lang['config_company_name_is_required'] = 'Le nom de l&apos;entreprise est requis';
$lang['config_company_start_date_is_required'] = 'La date de début de l&apos;entreprise est requise';
$lang['config_company_address_one_is_required'] = 'L&apos;adresse de l&apos;entreprise 1 est requise';
$lang['config_company_address_two_is_required'] = 'L&apos;adresse de l&apos;entreprise 2 est requise';
$lang['config_company_city_is_required'] = 'La ville de l&apos;entreprise est requise';
$lang['config_company_postal_code_is_required'] = 'Le code postal de l&apos;entreprise est requis';
$lang['config_company_country_is_required'] = 'Le pays de l&apos;entreprise est requis';
$lang['config_accounts_payable_code_is_required'] = 'Le code des comptes fournisseurs est requis';
$lang['config_accounts_receivable_is_required'] = 'Les comptes clients sont requis';
$lang['config_accounts_inventory_control_is_required'] = 'Le contrôle des stocks est requis';
$lang['config_accounts_asset_control_is_required'] = 'Le compte de contrôle des actifs est requis';
$lang['config_payroll_control_account_is_required'] = 'Le compte de contrôle de la paie est requis';
$lang['config_unbilled_grv_is_required'] = 'Le GRV non facturé est requis';
$lang['config_state_is_required'] = 'L&apos;état est requis';
$lang['config_account_type_is_required'] = 'Le type de compte est requis';
$lang['config_account_code_is_required'] = 'Le code du compte est requis';
$lang['config_control_account_description_is_required'] = 'La description du compte de contrôle est requise';
$lang['config_control_is_master_account_is_required'] = 'Le compte principal est requis';
$lang['config_the_changes_you_have_made'] = 'Les changements que vous avez effectués n&apos;affecteront pas les enregistrements saisis précédemment, ils ne seront appliqués que pour les transactions futures';
$lang['config_company_create_url'] = 'URL de création du client tiers';
$lang['config_company_update_url'] = 'URL de mise à jour du client tiers';
$lang['config_company_urls'] = 'URL';
$lang['config_step_five'] = 'Étape 5 ';

/*Approval Setup*/
$lang['config_approval_setup'] = 'Configuration de l&apos;approbation';
$lang['config_level_no'] = 'Numéro de niveau';
$lang['config_add_new_user_approval'] = 'Ajouter une nouvelle approbation d&apos;utilisateur';
$lang['config_select_level'] = 'Sélectionner le niveau';
$lang['config_save_approva_user'] = 'Enregistrer l&apos;utilisateur d&apos;approbation';
$lang['config_level_no_is_required'] = 'Le numéro de niveau est requis';
$lang['config_you_want_to_delete_this_file'] = 'Vous voulez supprimer ce fichier !';

/*Navigation Group Setup*/
$lang['config_navigation_group_setup'] = 'Configuration du groupe de navigation';
$lang['config_navigation_description_setup'] = 'Configuration de la description de la navigation';
$lang['config_user_group'] = 'Groupe d&apos;utilisateurs';
$lang['config_add_employees'] = 'Ajouter des employés';
$lang['config_emp_id'] = 'ID Employé';
$lang['config_navigation_access'] = 'Accès à la navigation';
/*company user Group Setup*/
$lang['config_create_user_group'] = 'Créer un groupe d&apos;utilisateurs';
$lang['config_widget'] = 'Widget';

/*User Managment*/
$lang['config_user_management'] = 'Gestion des utilisateurs';
$lang['config_username'] = 'Nom d&apos;utilisateur';
$lang['config_password'] = 'Mot de passe';
$lang['config_password_changed'] = 'Mot de passe modifié';
$lang['config_password_login_attempt'] = 'Tentative de connexion';
$lang['config_password_login_active'] = 'Connexion active';
$lang['config_strong'] = 'Fort';
$lang['config_medium'] = 'Moyenne';
$lang['config_weak'] = 'Faible';
$lang['config_super_admin'] = 'Super administrateur';   
/*Document Setup*/
$lang['config_document_setup'] = 'Configuration des documents';
$lang['config_standard_document_code'] = 'Code de document standard';
$lang['config_financial_document_code'] = 'Code de document financier';
$lang['config_prefix'] = 'Préfixe';
$lang['config_serial_no'] = 'Numéro de série';
$lang['config_format_length'] = 'Longueur du format';
$lang['config_format_one'] = 'Format 1';
$lang['config_format_two'] = 'Format 2';
$lang['config_format_three'] = 'Format 3';
$lang['config_format_four'] = 'Format 4';
$lang['config_format_five'] = 'Format 5';
$lang['config_format_six'] = 'Format 6';
/*Report Template*/
$lang['config_report_template'] = 'Modèle de rapport';
$lang['config_create_template'] = 'Créer un modèle';
$lang['config_report_template_master'] = 'Modèle principal de rapport';
$lang['config_report_template_detail'] = 'Détail du modèle de rapport';
$lang['config_report_template_link'] = 'Lien du modèle de rapport';
/*Administration*/
$lang['config_revising_approved_document'] = 'Inverser le document approuvé';
$lang['config_document_type'] = 'Type de document';
$lang['config_reverse_this_document'] = 'Inverser ce document';
$lang['config_unable_to_prcess'] = 'Impossible de traiter';
$lang['config_system_code'] = 'Code système';
$lang['config_auto_id_is_required'] = 'L&apos;ID automatique est requis';
$lang['config_document_id_is_required'] = 'L&apos;ID du document est requis';
$lang['config_document_code_is_required'] = 'Le code du document est requis';
/*company policy*/
$lang['config_company_policy'] = 'Politique de l&apos;entreprise';
$lang['config_select_a_document'] = 'Sélectionner un document';
$lang['config_password_complexity_configuration'] = 'Configuration de la complexité du mot de passe';
$lang['config_minimum_length'] = 'Longueur minimale';
$lang['config_maximum_length'] = 'Longueur maximale';
$lang['config_capital_length'] = 'Lettres majuscules';
$lang['config_special_characters'] = 'Caractères spéciaux';

$lang['config_mpr_template'] = 'Modèle MPR';
$lang['config_share_holding'] = 'Détention d&apos;actions';
$lang['config_share_holder_name'] = 'Nom du détenteur d&apos;actions';
$lang['config_update_sort_order'] = 'Mettre à jour l&apos;ordre de tri';
$lang['config_new_header_or_group_total'] = 'Nouveau total de groupe ou en-tête';
$lang['config_new_item'] = 'Nouvel article';
$lang['config_title_Edit'] = 'Modifier le titre';
$lang['config_user_type'] = 'Type d&apos;utilisateur';
$lang['config_terms_and_conditions'] = 'Termes et conditions';
$lang['config_add_notes'] = 'Ajouter des notes';
$lang['config_edit_description'] = 'Modifier la description';

$lang['config_tax_card_no'] = 'Numéro de carte fiscale';
$lang['config_you_want_to_open_this_control_account'] = 'Vous voulez ouvrir ce compte de contrôle ?';
$lang['config_you_want_to_close_this_control_account'] = 'Vous voulez fermer ce compte de contrôle ?';
$lang['support_token'] = 'Jeton de support';
$lang['config_support_token'] = 'Jeton de support';
$lang['config_email_token'] = 'Jeton de courriel'; 


