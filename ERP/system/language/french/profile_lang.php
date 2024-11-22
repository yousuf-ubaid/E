<?php
/**
 * System messages translation for CodeIgniter(tm)
 *
 * @prefix: profile_
 */
defined('BASEPATH') OR exit('No direct script access allowed');


$languageflowserve = getPolicyValues('LNG', 'All');

$lang['profile_my_profile'] = 'Mon Profil';

$lang['profile_personal_detail'] = 'Détails Personnels';
$lang['profile_employee_id'] = 'ID Employé';
$lang['profile_full_name'] = 'Nom Complet';
$lang['profile_surname'] = 'Nom de Famille';
$lang['profile_date_of_birth'] = 'Date de Naissance';
$lang['profile_marital_status'] = 'État Civil';
$lang['profile_blood_group'] = 'Groupe Sanguin';
$lang['profile_employment_data'] = 'Données d&apos;Emploi';

$lang['profile_change_password'] = 'Changer le Mot de Passe';
$lang['profile_current_password'] = 'Mot de Passe Actuel';
$lang['profile_new_password'] = 'Nouveau Mot de Passe';
$lang['profile_confirm_password'] = 'Confirmer le Mot de Passe';

/*Fiche de Paie*/
$lang['profile_pay_slip'] = 'Fiche de Paie';
$lang['profile_filter'] = 'Filtrer';
$lang['profile_payroll_type'] = 'Type de Paie';
$lang['profile_Month'] = 'Mois';
$lang['profile_load'] = 'Charger';
$lang['profile_monthly_allowance'] = 'Fiche de Paie - Allocation Mensuelle';
$lang['profile_payroll'] = 'Paie';
$lang['profile_non_payroll'] = 'Hors Paie';

/*Demande de Frais*/
$lang['profile_expense_claim'] = 'Demande de Remboursement';
$lang['profile_expense_claim_approval'] = 'Approbation de la Demande de Remboursement';
$lang['profile_monthly_allowance'] = 'Code du Document';
$lang['profile_total_value'] = 'Valeur Totale';
$lang['profile_file_name'] = 'Nom du Fichier';
$lang['profile_add_expense_claim'] = 'Ajouter une Demande de Remboursement';
$lang['profile_Step1_expense_claim_header'] = 'Étape 1 - En-tête de la Demande de Remboursement';
$lang['profile_Step2_expense_claim_detail'] = 'Étape 2 - Détail de la Demande de Remboursement';
// $lang['profile_Step3_expense_claim_detail'] = 'Étape 3 - Détail de l&apos;Attachement';
$lang['profile_Step3_expense_claim_confirmation'] = 'Étape 3 - Confirmation de la Demande de Remboursement';
$lang['profile_expense_claim_detail'] = 'Détail de la Demande de Remboursement';
$lang['profile_expense_claim_attachments'] = 'Pièces Jointes de la Demande de Remboursement';
$lang['profile_expense_claim_category'] = 'Catégorie de la Demande de Remboursement';
$lang['profile_doc_reference'] = 'Réf. Doc';
$lang['profile_add_expense_claim'] = 'Ajouter une Demande de Remboursement';

if (in_array($languageflowserve, ['MSE', 'SOP', 'GCC','Nov', 'Flowserve','Micoda'])) {
    $lang['profile_expense_claim'] = 'Demande d&apos;Indemnité';
    $lang['profile_expense_claim_approval'] = 'Approbation de la Demande d&apos;Indemnité';
    $lang['profile_monthly_allowance'] = 'Code du Document';
    $lang['profile_total_value'] = 'Valeur Totale';
    $lang['profile_file_name'] = 'Nom du Fichier';
    $lang['profile_add_expense_claim'] = 'Ajouter une Demande d&apos;Indemnité';
    $lang['profile_Step1_expense_claim_header'] = 'Étape 1 - En-tête de la Demande d&apos;Indemnité';
    $lang['profile_Step2_expense_claim_detail'] = 'Étape 2 - Détail de la Demande d&apos;Indemnité';
    // $lang['profile_Step3_expense_claim_detail'] = 'Étape 3 - Détail de l&apos;Attachement';
    $lang['profile_Step3_expense_claim_confirmation'] = 'Étape 3 - Confirmation de la Demande d&apos;Indemnité';
    $lang['profile_expense_claim_detail'] = 'Détail de la Demande d&apos;Indemnité';
    $lang['profile_expense_claim_attachments'] = 'Pièces Jointes de la Demande d&apos;Indemnité';
    $lang['profile_expense_claim_category'] = 'Catégorie de la Demande d&apos;Indemnité';
    $lang['profile_doc_reference'] = 'Réf. Doc';
    $lang['profile_add_expense_claim'] = 'Ajouter une Demande d&apos;Indemnité';
} else {
    
    $lang['profile_expense_claim'] = 'Demande de Remboursement';
    $lang['profile_expense_claim_approval'] = 'Approbation de la Demande de Remboursement';
    $lang['profile_monthly_allowance'] = 'Code du Document';
    $lang['profile_total_value'] = 'Valeur Totale';
    $lang['profile_file_name'] = 'Nom du Fichier';
    $lang['profile_add_expense_claim'] = 'Ajouter une Demande de Remboursement';
    $lang['profile_Step1_expense_claim_header'] = 'Étape 1 - En-tête de la Demande de Remboursement';
    $lang['profile_Step2_expense_claim_detail'] = 'Étape 2 - Détail de la Demande de Remboursement';
    // $lang['profile_Step3_expense_claim_detail'] = 'Étape 3 - Détail de l&apos;Attachement';
    $lang['profile_Step3_expense_claim_confirmation'] = 'Étape 3 - Confirmation de la Demande de Remboursement';
    $lang['profile_expense_claim_detail'] = 'Détail de la Demande de Remboursement';
    $lang['profile_expense_claim_attachments'] = 'Pièces Jointes de la Demande de Remboursement';
    $lang['profile_expense_claim_category'] = 'Catégorie de la Demande de Remboursement';
    $lang['profile_doc_reference'] = 'Réf. Doc';
    $lang['profile_add_expense_claim'] = 'Ajouter une Demande de Remboursement';
}



/*Employee Leave Application*/
$lang['profile_add_leave'] = 'Nouveau Congé';
$lang['profile_document_code'] = 'Code Document';
$lang['profile_leave_type'] = 'Type de Congé';

$lang['profile_employee_leave_application'] = 'Demande de Congé Employé';
$lang['profile_employee_name'] = 'Nom de l&apos;Employé';
$lang['profile_starting_date'] = 'Date de Début';
$lang['profile_ending_date'] = 'Date de Fin';
$lang['profile_half_day'] = 'Demi-Journée';
$lang['profile_leave_entitled'] = 'Congé Autorisé';
$lang['profile_leave_applied'] = 'Congé Demandé';
$lang['profile_balance'] = 'Solde';

$lang['profile_taken'] = 'Pris';
$lang['profile_policy'] = 'Politique';
$lang['profile_leave_detail'] = 'Détail du Congé';

$lang['profile_approval'] = 'Approbation';
$lang['profile_approval_users'] = 'Utilisateurs d&apos;Approbation';
$lang['profile_approval_level'] = 'Niveau d&apos;Approbation';

$lang['profile_add_item_detail'] = 'Ajouter Détail de l&apos;Article';
$lang['profile_edit_item_detail'] = 'Modifier Détail de l&apos;Article';
$lang['profile_discount_0_100'] = 'Le pourcentage de remise doit être entre 0 - 100';
$lang['profile_discount_unit_cost'] = 'Le montant de la remise doit être inférieur au coût unitaire';
$lang['profile_cancelled'] = 'Annulé';

$lang['profile_emp_code'] = 'Code Employé';
$lang['profile_designation'] = 'Désignation';
$lang['profile_please_select_an_employee_to_continue'] = 'Veuillez sélectionner un employé pour continuer';
$lang['profile_Personal_email'] = 'Email Personnel';
$lang['profile_visa_expiry_date'] = 'Date d&apos;Expiration du Visa';
$lang['profile_date_of_join'] = 'Date d&apos;Entrée';
$lang['profile_man_power_no'] = 'Numéro de Main-d&apos;Œuvre';
$lang['profile_departments'] = 'Départements';
$lang['profile_report_manager'] = 'Manager Responsable';
$lang['profile_family_details'] = 'Détails de la Famille';
$lang['profile_family_documents'] = 'Documents';
$lang['profile_bank_details'] = 'Détails Bancaires';
$lang['profile_my_employee_list'] = 'Ma Liste des Employés';
$lang['profile_current_password'] = 'Mot de Passe Actuel';
$lang['profile_add_family_detail'] = 'Ajouter Détail de la Famille';
$lang['profile_relationship'] = 'Relation';
$lang['profile_familydetail'] = 'Détail de la Famille';
$lang['profile_passport_expiry_date'] = 'Date d&apos;Expiration du Passeport';
$lang['profile_visa_no'] = 'Numéro de Visa';
$lang['profile_visa_expiry_date'] = 'Date d&apos;Expiration du Visa';
$lang['profile_relationship_status'] = 'Statut de la Relation';

$lang['profile_education'] = 'Éducation';

$lang['profile_sales_target_achieved'] = 'Objectif de Ventes Atteint';
$lang['profile_add_sales_target_achieved'] = 'Ajouter Objectif de Ventes';
$lang['profile_update_sales_target_achieved'] = 'Mettre à Jour l&apos;Objectif de Ventes Atteint';
$lang['profile_date_from_is_required'] = 'La date de début est requise';
$lang['profile_amount_is_required'] = 'Le montant est requis';

$lang['profile_period'] = 'Période';
$lang['profile_target_amount'] = 'Montant de l&apos;Objectif';

$lang['profile_achived_amount'] = 'Montant Atteint';
$lang['profile_there_are_no_sales'] = 'AUCUN OBJECTIF DE VENTES ATTEINT À AFFICHER';

$lang['profile_payroll_nor_run_on_selected_month_for_you'] = 'La paie n&apos;a pas été exécutée pour vous au mois sélectionné';
$lang['profile_segment'] = 'Segment';

$lang['profile_earnings'] = 'Gains';
$lang['profile_total_earnings'] = 'Gains Totaux';
$lang['profile_deductions'] = 'Déductions';
$lang['profile_installment_no'] = 'Numéro de Versement';
$lang['profile_total_deductions'] = 'Déductions Totales';
$lang['profile_net_pay'] = 'Salaire Net';
$lang['profile_salary_transfer_details'] = 'Détails du Transfert de Salaire';
$lang['profile_loan_details'] = 'Détails du Prêt';
$lang['profile_pending_amount'] = 'Montant en Attente';
$lang['profile_loan_code'] = 'Code de Prêt';
$lang['profile_no_pending_nstallments'] = 'Aucune. Versements en Attente';
$lang['profile_leave_details'] = 'Détails des Congés';
$lang['profile_entitled'] = 'Autorisé';

$lang['profile_pay_slip_for_the_month_of'] = 'Fiche de Paie pour le Mois de';
$lang['profile_pay_slip_capital'] = 'FICHE DE PAIE';
$lang['profile_employee_no'] = 'Numéro d&apos;Employé';
$lang['profile_basic_br_allowance'] = 'Allocation de Base+BR';
$lang['profile_deduction_as_direct'] = 'Déduction en Direct';
$lang['profile_net_remuneration'] = 'Rémunération Nette';
$lang['profile_emp_funtion'] = 'Fonction';
$lang['profile_emp_bussines_level_deviion'] = 'Niveau Commercial-Division';
$lang['profile_emp_bussines_level_segment'] = 'Niveau Commercial-Segment';
$lang['profile_emp_bussines_level_sub_segment'] = 'Niveau Commercial-Sous-Segment';

$lang['profile_change_profile_pic'] = 'Changer la Photo de Profil';