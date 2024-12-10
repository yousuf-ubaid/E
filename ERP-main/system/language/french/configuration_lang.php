<?php

/**
 * System messages translation for CodeIgniter(tm)
 *
 * @prefix: config_
 */
defined('BASEPATH') or exit('No direct script access allowed');

/** Common */
$lang['config_common_create_user_group'] = 'Créer un groupe d&apos;utilisateurs';
$lang['config_common_sub_group'] = 'Sous-groupe';
$lang['config_common_user_group'] = 'Groupe d&apos;utilisateurs';
$lang['config_common_main_group'] = 'Groupe principal';
$lang['config_common_inactive'] = 'Inactif';
$lang['config_common_add_link'] = 'Ajouter un lien';
$lang['config_common_step_one'] = 'Étape 1';
$lang['config_common_add_save'] = 'Ajouter et enregistrer';
$lang['config_common_supplier_code'] = 'Code fournisseur';
$lang['config_common_supplier_details'] = 'Détails du fournisseur';
$lang['config_common_you_want_to_delete_this_supplier'] = 'Vous voulez supprimer ce fournisseur !';
$lang['config_common_add_supplier'] = 'Ajouter un fournisseur';
/*Company Sub Groups*/
$lang['config_company_sub_groups'] = 'Sous-groupes de l&apos;entreprise';
$lang['config_company_sub_group_edit'] = 'Modifier le sous-groupe';
$lang['config_main_group_is_required'] = 'Le groupe principal est requis';
$lang['config_add_employees'] = 'Ajouter des employés';
$lang['config_emp_id'] = 'ID Employé';
$lang['config_navigation_access'] = 'Accès à la navigation';
$lang['config_customer_code_is_required'] = 'Le code client est requis';
$lang['config_customer_name_is_required'] = 'Le nom du client est requis';
/*Group Conciladation*/
$lang['config_customer_master'] = 'Client principal';
$lang['config_create_customer'] = 'Créer un client';
$lang['config_add_new_customer'] = 'Ajouter un nouveau client';
$lang['config_group_customer_code'] = 'Code client du groupe';
$lang['config_group_customer_details'] = 'Détails du client du groupe';
$lang['config_customer_link'] = 'Lien client';
$lang['config_you_want_to_delete_this_customer'] = 'Vous voulez supprimer ce client !';
$lang['config_customer_header'] = 'En-tête du client';
$lang['config_customer_secondary_code'] = 'Code secondaire du client';
$lang['config_customer_receivable_account'] = 'Compte à recevoir';
$lang['config_customer_currency'] = 'Monnaie du client';
$lang['config_add_customer'] = 'Ajouter un client';
$lang['config_customer_code'] = 'Code client';
$lang['config_customer_company'] = 'Entreprise cliente';
$lang['config_customer_details'] = 'Détails du client';
$lang['config_gl_descriprion'] = 'Description GL';
$lang['config_gl_receivable_account_is_required'] = 'Le compte à recevoir est requis';
$lang['config_gl_receivable_customer_currency_is_required'] = 'La monnaie du client est requise';
$lang['config_update_customer'] = 'Mettre à jour le client';
/*Supplier Master*/
$lang['config_supplier_master'] = 'Fournisseur principal';
$lang['config_add_new_supplier'] = 'Ajouter un nouveau fournisseur';
$lang['config_create_supplier'] = 'Créer un fournisseur';
$lang['config_supplier_link'] = 'Lien fournisseur';
$lang['config_supplier_name'] = 'Nom du fournisseur';
$secondary_code = getPolicyValues('SCAC', 'All');

if($secondary_code == 1){
    $lang['config_secondary_code'] = 'Code de compte';
}else{
    $lang['config_secondary_code'] = 'Code secondaire';
}

$lang['config_supplier_header'] = 'En-tête du fournisseur';
$lang['config_liability_account'] = 'Compte de passif';
$lang['config_supplier_code_is_required'] = 'Le code fournisseur est requis';
$lang['config_supplier_name_is_required'] = 'Le nom du fournisseur est requis';
$lang['config_liability_account_is_required'] = 'Le compte de passif est requis';
$lang['config_supplier_currency_is_required'] = 'La monnaie du fournisseur est requise';
$lang['config_update_supplier'] = 'Mettre à jour le fournisseur';
$lang['config_customer_country'] = 'Pays du client';
$lang['config_secondary_address'] = 'Adresse secondaire';
$lang['config_primary_address'] = 'Adresse principale';
$lang['config_credit_limit'] = 'Limite de crédit';
$lang['config_credit_period'] = 'Période de crédit';
$lang['config_identification_no'] = 'Numéro d&apos;identification';
$lang['config_name_on_cheque'] = 'Nom sur le chèque';
$lang['config_chart_of_accounts'] = 'Plan comptable';
$lang['config_chart_of_accounts_or_category_not_linked '] = 'Plan comptable ou catégorie non liée';
$lang['config_duplicate'] = 'Dupliquer';
$lang['config_chart_of_account_replication '] = 'Réplication du plan comptable';
$lang['config_chart_of_account_link '] = 'Lien du plan comptable';
$lang['config_bank_currency'] = 'Monnaie de la banque';
$lang['config_bank_swift_code'] = 'Code SWIFT de la banque';
$lang['config_bank_brach'] = 'Agence bancaire';
$lang['config_check_number'] = 'Numéro de chèque';
$lang['config_bank_name'] = 'Nom de la banque';
$lang['config_master_account'] = 'Compte principal';
$lang['config_find_gl'] = 'Trouver le GL';
$lang['config_segment_group'] = 'Groupe de segments';
$lang['config_segment_group_replication'] = 'Réplication du groupe de segments';
$lang['config_segment_name'] = 'Nom du segment';
$lang['config_segment_link'] = 'Lien du segment';
$lang['config_segment_code'] = 'Code du segment';
$lang['config_create_segment'] = 'Créer un segment';
$lang['config_add_new_segment'] = 'Ajouter un nouveau segment';
$lang['config_item_master_link'] = 'Lien de l&apos;article principal';
$lang['config_add_new_item'] = 'Ajouter un nouvel article';
$lang['config_add_new_category'] = 'Ajouter une nouvelle catégorie';
$lang['config_edit_category'] = 'Modifier la catégorie';
$lang['config_common_supplier_category'] = 'Catégorie de fournisseur';
$lang['config_customer_category_replication'] = 'Réplication de la catégorie client';
$lang['config_supplier_category_replication'] = 'Réplication de la catégorie fournisseur';
$lang['config_uom_replication'] = 'Réplication de l&apos;unité de mesure';
$lang['config_warehouse_replication'] = 'Réplication de l&apos;entrepôt';
$lang['config_group_financial_year'] = 'Exercice financier du groupe';
$lang['config_create_financial_year'] = 'Créer un exercice financier';
$lang['config_create_new_group_financial_year'] = 'Ajouter un nouvel exercice financier du groupe';
$lang['config_group_warehouse_master'] = 'Maître d&apos;entrepôt du groupe';
$lang['config_bom_number'] = 'Numéro du BOM';
