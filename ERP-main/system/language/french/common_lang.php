<?php

/**
 * System messages translation for CodeIgniter(tm)
 */
defined('BASEPATH') or exit('No direct script access allowed');

$ray_Company_document_policy = getPolicyValues('RCDDP', 'All');
$language_policy = getPolicyValues('LNG', 'All');


/** Common */
$lang['common_add'] = 'Ajouter';
$lang['common_save'] = 'Sauvegarder';
$lang['common_save_and_confirm'] = 'Sauvegarder & Confirmer';
$lang['common_save_and_next'] = 'Sauvegarder & Suivant';
$lang['common_save_as_draft'] = 'Sauvegarder en tant que Brouillon';
$lang['common_edit'] = 'Modifier';
$lang['common_update'] = 'Mettre à jour';
$lang['common_confirm'] = 'Confirmer';
$lang['common_Close'] = 'Fermer';
$lang['common_is_active'] = 'Est Actif';
$lang['common_in_active'] = 'Inactif';
$lang['common_action'] = 'Action'; // ok
$lang['common_description'] = 'Description';
$lang['common_is_variable'] = 'Est un Paiement Variable';
$lang['common_status'] = 'Statut';
$lang['common_all'] = 'Tous';
$lang['common_active'] = 'Actif';
$lang['common_code'] = 'Code';
$lang['common_name'] = 'Nom';
$lang['common_account_no'] = 'Numéro de Compte';
$lang['common_holder'] = 'Titulaire';
$lang['common_bank'] = 'Banque';
$lang['common_branch'] = 'Agence';
$lang['common_pull_from_attendance'] = 'Est Extraire de la Présence';


$lang['common_previous'] = 'Précédent';

$lang['common_gender'] = 'Genre';
$lang['common_religion'] = 'Religion';
$lang['common_nationality'] = 'Nationalité';
$lang['common_address'] = 'Adresse';
$lang['common_mobile'] = 'Mobile';
$lang['common_telephone'] = 'Téléphone';
$lang['common_email'] = 'Email';
$lang['common_initial'] = 'Initiale';
$lang['common_title'] = 'Titre';
$lang['common_phone'] = 'Téléphone';
$lang['common_web'] = 'Site Web';
$lang['common_user_name'] = 'Nom d&apos;utilisateur';
$lang['common_password'] = 'Mot de passe';
$lang['common_telephone_is_required'] = 'Le téléphone est requis';

$lang['common_yes'] = 'Oui';
$lang['common_cancel'] = 'Annuler';
$lang['common_shift'] = 'Quart';

$lang['common_details'] = 'Détails';
$lang['common_approved'] = 'Approuvé';
$lang['common_is_confirmed'] = 'Est confirmé';
$lang['common_confirmed'] = 'Confirmé';
$lang['common_not_confirmed'] = 'Non confirmé';
$lang['common_not_approved'] = 'Non approuvé';
$lang['common_file_name'] = 'Nom du fichier';
$lang['common_file'] = 'Fichier';
$lang['common_type'] = 'Type';
$lang['common_date'] = 'Date';
$lang['common_to'] = 'À';
$lang['common_status'] = 'Statut';
$lang['common_clear'] = 'Effacer';
$lang['common_refer_back'] = 'Renvoi';
$lang['common_day_month'] = 'Jours dans le mois';


if (in_array($language_policy, ['MSE', 'SOP', 'GCC','Nov', 'Flowserve','Micoda'])) {
    $lang['common_segment'] = 'Centre de coûts';
}else{
    $lang['common_segment'] = 'Segment';
}


$lang['common_currency'] = 'Monnaie';
$lang['common_amount'] = 'Montant';
$lang['common_no_records_found'] = 'Aucun enregistrement trouvé';
$lang['common_attachments'] = 'Pièces jointes';
$lang['common_add_attachments'] = 'Ajouter des pièces jointes';
$lang['common_edit_attachments'] = 'Modifier les pièces jointes';

$lang['common_view'] = 'Voir';
$lang['common_comment'] = 'Commentaire';
$lang['common_month'] = 'Mois';
$lang['common_confirmed_date'] = 'Date de confirmation';
$lang['common_confirmed_by'] = 'Confirmé par';
$lang['common_confirmed_on'] = 'Le';
$lang['common_approved_by'] = 'Approuvé par';

$lang['common_approved_date'] = 'Date d&apos;approbation';
$lang['common_level'] = 'Niveau';
$lang['common_levels'] = 'Niveaux';

$lang['common_are_you_sure'] = 'Êtes-vous sûr ?';
$lang['common_you_want_to_delete'] = 'Vous voulez supprimer cet enregistrement !';
$lang['common_you_want_to_delete_all_records_of_this_employee'] = 'Vous voulez supprimer tous les enregistrements de cet employé !';
$lang['common_you_want_to_delete_all'] = 'Vous voulez supprimer tous les enregistrements !';
$lang['common_you_want_to_refer_back'] = 'Vous voulez renvoyer !';
$lang['common_you_want_to_refer_back_cancellation'] = 'Vous voulez renvoyer cette annulation !';
$lang['common_you_want_to_cancel'] = 'Vous voulez annuler !';
$lang['common_you_want_to_confirm_this_document'] = 'Vous voulez confirmer ce document !';
$lang['common_you_want_to_generate_sales_order'] = 'Vous voulez générer une commande de vente !';
$lang['common_you_want_to_save_this_document'] = 'Vous voulez enregistrer ce document !';
$lang['common_you_want_to_edit_this_document'] = 'Vous voulez modifier ce document !';
$lang['common_you_want_to_edit_this_record'] = 'Vous voulez modifier cet enregistrement !';
$lang['common_delete'] = 'Supprimer';
$lang['common_cancel'] = 'Annuler';
$lang['common_company_id'] = 'ID de l&apos;entreprise';
$lang['common_percentage'] = 'Pourcentage';
$lang['common_category'] = 'Catégorie';
$lang['common_partially_approved'] = 'Partiellement approuvé';
$lang['common_from'] = 'De';
$lang['common_no'] = 'Non';
$lang['common_confirmation'] = 'Confirmation';
$lang['common_are_you_sure_you_want_to_close_the_counter'] = 'Êtes-vous sûr de vouloir fermer le comptoir ?';

$lang['common_are_you_sure_you_want_to_make_this_as_p_d'] = 'Vous voulez définir cet enregistrement comme désignation principale !';

$lang['common_are_you_sure_you_want_to'] = 'Vous voulez';


$lang['common_filters'] = 'Filtres';
$lang['common_search'] = 'Rechercher';
$lang['common_submit'] = 'Soumettre';
$lang['common_report'] = 'Rapport';
$lang['common_form'] = 'Formulaire';
$lang['common_generate'] = 'Générer';
$lang['common_print'] = 'Imprimer';
$lang['common_submit_and_print'] = 'Soumettre et Imprimer';
$lang['common_submit_and_close'] = 'Soumettre et Fermer';

$lang['common_step'] = 'Étape';
$lang['common_no_attachment_found'] = 'Aucune pièce jointe trouvée';
$lang['common_previous'] = 'Précédent';
$lang['common_save_change'] = 'Sauvegarder les modifications';
$lang['common_update_changes'] = 'Mettre à jour les modifications';

$lang['common_gl_code'] = 'Code GL';
$lang['common_next'] = 'Suivant';
$lang['common_pending'] = 'En attente';
$lang['common_skipped'] = 'Sauté';
$lang['common_closed'] = 'Fermé';
$lang['common_canceled'] = 'Annulé';
$lang['common_canceled_req'] = 'Demande d&apos;annulation';
$lang['common_not_closed'] = 'Non fermé';

$lang['common_field'] = 'Champ';
$lang['common_bom_number'] = 'Numéro BOM';

$lang['common_Upload'] = 'Télécharger';
$lang['common_designation'] = 'Désignation';
$lang['common_designation_simple'] = 'désignation';
$lang['common_update_and_Confirm'] = 'Mettre à jour et Confirmer';
$lang['common_update_add_new'] = 'Ajouter nouveau';
$lang['common_monthly'] = 'Mensuel';

$lang['common_column'] = 'Colonne';

$lang['common_load'] = 'Charger';
$lang['common_clear_all'] = 'Tout effacer';
$lang['common_processed'] = 'Traité';
$lang['common_total'] = 'Total';
$lang['common_pay'] = 'Payer';

$lang['common_by_cash'] = 'En espèces';
$lang['common_default'] = 'Par défaut';
$lang['common_group'] = 'Groupe';
$lang['common_new'] = 'Nouveau';
$lang['common_note'] = 'Note';
$lang['common_reason'] = 'Raison';
$lang['common_no_records_available'] = 'Aucun enregistrement disponible';
$lang['common_add_detail'] = 'Ajouter un détail';
$lang['common_create_new'] = 'Créer nouveau';
$lang['common_company'] = 'Entreprise';
$lang['common_select_type'] = 'Sélectionner le type';
$lang['common_select_a_option'] = 'Sélectionner une option';
$lang['common_select_all'] = 'Sélectionner tout';
$lang['common_add_all'] = 'Ajouter tout';
$lang['common_records_not_found'] = 'Enregistrements non trouvés';
$lang['common_day'] = 'Jour';
$lang['common_notes'] = 'Notes';
$lang['common_customer'] = 'Client';
$lang['common_payment'] = 'Paiement';
$lang['common_please_select'] = 'Veuillez sélectionner';
$lang['common_non'] = 'Aucun';
$lang['common_processing'] = 'Traitement';
$lang['common_menu'] = 'Menu';
$lang['common_image'] = 'Image';
$lang['common_item'] = 'Article';
$lang['common_crew'] = 'Équipage';
$lang['common_tables'] = 'Tables';
$lang['common_price'] = 'Prix';
$lang['common_select_employee'] = 'Sélectionner un employé';
$lang['common_Location'] = 'Emplacement';
$lang['common_warehouse'] = 'Entrepôt';
$lang['common_ok'] = 'D&apos;accord';
$lang['common_time'] = 'Temps';
$lang['common_cash'] = 'Espèces';
$lang['common_visa'] = 'VISA';
$lang['common_master_card'] = 'Carte Master';
$lang['common_cheque'] = 'Chèque';
$lang['common_change'] = 'Changement';


$lang['common_formula_builder'] = 'Créateur de formule';
$lang['common_add_formula'] = 'Ajouter une formule';
$lang['common_balance_payment'] = 'Paiement du solde';
$lang['common__monthly_addition'] = 'Ajout mensuel';
$lang['common__monthly_deduction'] = 'Déduction mensuelle';

$lang['common_an_error_occurred_Please_try_again'] = 'Une erreur est survenue! Veuillez réessayer';
$lang['common_paysheet'] = 'Feuille de paie';
$lang['common_not_confirmed_yet'] = 'Pas encore confirmé';
$lang['common_please_refresh_and_try_again'] = 'Veuillez actualiser et réessayer';
$lang['common_failed'] = 'Échoué';
$lang['common_error'] = 'Erreur';
$lang['common_success'] = 'Succès!';
$lang['common_warning'] = 'Avertissement!';
$lang['common_information'] = 'Information';
$lang['common_not_found'] = 'Non trouvé';
$lang['common_no_data_available_in_table'] = 'Aucune donnée disponible dans le tableau';
$lang['common_show'] = 'Afficher ';
$lang['common_select_relationship'] = 'Sélectionner la relation';
$lang['common_select_title'] = 'Sélectionner le titre';
$lang['common_select_nationality'] = 'Sélectionner la nationalité';
$lang['common_date_of_birth'] = 'Date de naissance';
$lang['common_male'] = 'Homme';
$lang['common_female'] = 'Femme';
$lang['common_attachment'] = 'Pièce jointe';
$lang['common_document'] = 'Document';
$lang['common_short_order'] = 'Commande courte';
$lang['common_passport'] = 'Passeport';
$lang['common_visa'] = 'Visa';
$lang['common_insurance'] = 'Assurance';
$lang['common_national_no'] = 'Numéro national';
$lang['common_id_no'] = 'Numéro d&apos;identification';
$lang['common_passport_number_no'] = 'Numéro de passeport';
$lang['common_insurance_category'] = 'Catégorie d&apos;assurance';
$lang['common_insurance_code'] = 'Code d&apos;assurance';
$lang['common_cover_from'] = 'Couverture à partir de';
$lang['common_approval_user'] = 'Utilisateur d&apos;approbation';
$lang['common_no_matching_records_found'] = 'Aucun enregistrement correspondant trouvé';
$lang['common_department'] = 'Département';
$lang['common_addition'] = 'Ajout';
$lang['common_deduction'] = 'Déduction';
$lang['common_select_floor'] = 'Sélectionner l&apos;étage';
$lang['common_data_changes'] = 'Modifications des données personnelles';
$lang['common_family_changes'] = 'Modifications des données familiales';
$lang['common_grand_total'] = 'Total général';
$lang['common_date_is_required'] = 'La date est requise';
$lang['common_description_is_required'] = 'La description est requise';
$lang['common_type_is_required'] = 'Le type est requis';
$lang['common_gl_code_is_required'] = 'Le code GL est requis';
$lang['common_number'] = 'Numéro';
$lang['common_after'] = 'Après';
$lang['common_before'] = 'Avant';
$lang['common_select_currency'] = 'Sélectionner la devise';
$lang['common_document_date_is_required'] = 'La date du document est requise';
$lang['common_currency_is_required'] = 'La devise est requise';
$lang['common_select'] = 'Sélectionner';
$lang['common_reporting_manager'] = 'Responsable hiérarchique';
$lang['common_referred_back_by'] = 'Renvoyé par';
$lang['common_select_leave_group'] = 'Sélectionner le groupe de congé';
$lang['common_start_hour_is_required'] = 'L&apos;heure de début est requise';
$lang['common_end_hour_is_required'] = 'L&apos;heure de fin est requise';
$lang['common_hourly_rate_is_required'] = 'Le taux horaire est requis';
$lang['common_estimatedQty'] = 'Quantité estimée';



$lang['common_employee_is_required'] = 'L&apos;employé est requis';
$lang['common_effective_date_is_required'] = 'La date d&apos;entrée en vigueur est requise';
$lang['common_new_amount_is_required'] = 'Le nouveau montant est requis';
$lang['common_category_is_required'] = 'La catégorie est requise';
$lang['common_please_fill_all_required_fields'] = 'Veuillez remplir tous les champs requis';

$lang['common_hr_documents'] = 'Documents RH';
$lang['common_add_employee'] = 'Ajouter un employé';
$lang['common_hours'] = 'Heures';
$lang['common_rate'] = 'Taux';
$lang['common_equivalent_hrs'] = 'Heures équivalentes';

$lang['common_customer_name'] = 'Nom du client';
$lang['common_value'] = 'Valeur';
$lang['common_approval'] = 'Approbation';
$lang['common_the_selected_file_is_not_valid'] = 'Le fichier sélectionné n&apos;est pas valide';
$lang['common_file_is_required'] = 'Le fichier est requis';
$lang['common_to_date_is_required'] = 'La date de fin est requise';
$lang['common_floor_is_required'] = 'L&apos;étage est requis';
$lang['common_search_name'] = 'Rechercher par nom';
$lang['common_from_date_is_required'] = 'La date de début est requise';
$lang['common_comments'] = 'Commentaires';
$lang['common_this_value_is_not_valid'] = 'Cette valeur n&apos;est pas valide';
$lang['common_employees'] = 'Employés';
$lang['common_employee_name'] = 'Nom de l&apos;employé';
$lang['common_start_date'] = 'Date de début';
$lang['common_invoice_number'] = 'Numéro de facture';
$lang['common_reference'] = 'Référence';
$lang['common_reference_number'] = 'Numéro de référence';
$lang['common_reference_no'] = 'Numéro de référence';
$lang['common_end_date'] = 'Date de fin';
$lang['common_please_fill_all_fields'] = 'Veuillez remplir tous les champs';
$lang['common_select_salary_category'] = 'Sélectionner la catégorie salariale';
$lang['common_account'] = 'Compte';
$lang['common_master_category_is_required'] = 'La catégorie principale est requise';
$lang['common_salary_category_is_required'] = 'La catégorie salariale est requise';
$lang['common_following_items_already_exist'] = 'Les éléments suivants existent déjà';
$lang['common_fax'] = 'Fax';
$lang['common_select_description'] = 'Sélectionner la description';

$lang['common_column'] = 'Colonne';
$lang['common_please_contac_support_team'] = 'Veuillez contacter l&apos;équipe de support';
$lang['common_approval_level'] = 'Niveau d&apos;approbation';
$lang['common_document_confirmed_by'] = 'Document confirmé par';
$lang['common_document_date'] = 'Date du document';
$lang['common_document_code'] = 'Code du document';
$lang['common_confirmed_date'] = 'Date de confirmation';
$lang['common_approved_date'] = 'Date d&apos;approbation';
$lang['common_date'] = 'Date';
$lang['common_document_not_approved_yet'] = 'Document non encore approuvé';
$lang['common_expense_claim'] = 'Demande de remboursement';
$lang['common_select_segment'] = 'Sélectionner un segment';
$lang['common_segment_is_required'] = 'Le segment est requis';
$lang['common_you_want_to_delete_this_attachment_file'] = 'Vous voulez supprimer ce fichier joint';
$lang['common_deleted_successfully'] = 'Supprimé avec succès';
$lang['common_deletion_failed'] = 'Échec de la suppression';
$lang['common_select_claim_category'] = 'Sélectionner la catégorie de réclamation';
$lang['common_expense_claim_attachments'] = 'Pièces jointes de la demande de remboursement';
$lang['common_select_gl_code'] = 'Sélectionner le code GL';
$lang['common_as_of_date'] = 'À la date du';
$lang['common_employee'] = 'Employé';
$lang['common_attendees'] = 'Participants';
$lang['common_please_select_at_least_one_employee_to_proceed'] = 'Veuillez sélectionner au moins un employé pour continuer';
$lang['common_first_month_is_required'] = 'Le premier mois est requis';
$lang['common_second_month_is_required'] = 'Le deuxième mois est requis';
$lang['common_mandatory'] = 'Obligatoire';
$lang['common_sort_order'] = 'Ordre de tri';
$lang['common_is_required'] = 'Est requis';
$lang['common_select_document'] = 'Sélectionner un document';
$lang['common_Country'] = 'Pays';
$lang['common_country_name'] = 'Nom du pays';
$lang['common_showing'] = 'Affichage';
$lang['common_of'] = 'de';
$lang['common_entries'] = 'entrées';
$lang['common_un_check_all'] = 'Tout décocher';
$lang['common_document_name_is_required'] = 'Le nom du document est requis';
$lang['common_supplier_name'] = 'Nom du fournisseur';
$lang['common_purchase_order'] = 'Bon de commande';
$lang['common_supplier'] = 'Fournisseur';
$lang['common_contact'] = 'Contact';
$lang['common_uom'] = 'UM';
$lang['common_qty'] = 'Quantité';
$lang['common_unit'] = 'Unité';
$lang['common_discount'] = 'Réduction';
$lang['common_net_cost'] = 'Coût net';
$lang['common_cost'] = 'Coût';
$lang['common_tax'] = 'Taxe';
$lang['common_transaction'] = 'Transaction';
$lang['common_referred_back'] = 'Renvoyé';
$lang['common_status_is_required'] = 'Le statut est requis';
$lang['common_document_approved_id_is_required'] = 'L&apos;ID du document approuvé est requis';
$lang['common_draft'] = 'Brouillon';
$lang['common_total_value'] = 'Valeur totale';
$lang['common_comments_are_required'] = 'Les commentaires sont requis';
$lang['common_you_want_to_re_open'] = 'Vous voulez rouvrir';
$lang['common_year'] = 'Année';

$lang['common_add_item'] = 'Ajouter un article';
$lang['common_unit_cost'] = 'Coût unitaire';
$lang['common_net_amount'] = 'Montant net';
$lang['common_item_id'] = 'ID de l&apos;article';
$lang['common_item_description'] = 'Description de l&apos;article';
$lang['common_select_uom'] = 'Sélectionner l&apos;UOM';
$lang['common_name_is_required'] = 'Le nom est requis';
$lang['common_supplier_currency_is_required'] = 'La devise du fournisseur est requise';
$lang['common_filter'] = 'Filtrer';
$lang['common_standard'] = 'Standard';
$lang['common_select_supplier'] = 'Sélectionner le fournisseur';
$lang['common_select_ship'] = 'Sélectionner le navire';
$lang['common_contact_number'] = 'Numéro de contact';
$lang['common_days'] = 'Jours';
$lang['common_project'] = 'Projet';
$lang['common_select_project'] = 'Sélectionner un projet';
$lang['common_you_want_to_change_leave_group'] = 'Voulez-vous changer de groupe de congé?';

$lang['common_directory'] = 'Annuaire';
$lang['common_full_name_is_required'] = 'Le nom complet est requis';
$lang['common_e_mail_required'] = 'E-mail requis';
$lang['common_gender_is_required'] = 'Le genre est requis';
$lang['common_narration'] = 'Narration';
$lang['common_joined_date'] = 'Date d&apos;adhésion';
$lang['common_manager'] = 'Manager';
$lang['common_select_a_nationality'] = 'Sélectionner une nationalité';
$lang['common_select_a_marital_status'] = 'Sélectionner un statut marital';
$lang['common_select_a_blood_group'] = 'Sélectionner un groupe sanguin';
$lang['common_select_a_religion'] = 'Sélectionner une religion';
$lang['common_select_country'] = 'Sélectionner un pays';
$lang['common_po_number'] = 'Numéro de commande PO';

$lang['common_relationship'] = 'Relation';

$lang['common_submitted'] = 'Soumis';
$lang['common_not_submitted'] = 'Non soumis';
$lang['common_document_is_required'] = 'Le document est requis';
$lang['common_supplier_invoice_attachments'] = 'Pièces jointes de la facture du fournisseur';
$lang['common_invoice_date'] = 'Date de la facture';
$lang['common_gl_details'] = 'Détails GL';
$lang['common_gl_code_description'] = 'Description du code GL';
$lang['common_gl_total'] = 'Total GL';
$lang['common_electronically_approved_by'] = 'Approuvé électroniquement par';

$lang['common_electronically_approved_date'] = 'Date d&apos;approbation électronique';
$lang['common_tax_total'] = 'Total des taxes';
$lang['common_debit_note_attachments'] = 'Pièces jointes de la note de débit';
$lang['common_remarks'] = 'Remarques';
$lang['common_system_stock'] = 'Stock système';
$lang['common_system_wac'] = 'WAC du système';
$lang['common_actual_stock'] = 'Stock réel';
$lang['common_actual_wac'] = 'WAC réel';

$lang['common_issue_date'] = 'Date d&apos;émission';
$lang['common_expire_date'] = 'Date d&apos;expiration';
$lang['common_issued_by'] = 'Émis par';
$lang['common_depreciation'] = 'Amortissement';
$lang['common_balance'] = 'Solde';
$lang['common_industry_type'] = 'Type d&apos;industrie';
$lang['common_emp_language_change'] = 'Voulez-vous changer la langue?';
$lang['common_statement'] = 'Déclaration';
$lang['common_template'] = 'Modèle';
$lang['common_period'] = 'Période';
$lang['you_want_to_change_payslip_visible_date'] = 'Voulez-vous changer la date visible du bulletin de paie?';
$lang['common_add_bulk_details'] = 'Ajouter des détails en masse';
$lang['common_proceed'] = 'Procéder';
$lang['common_employee_details'] = 'Détails de l&apos;employé';
$lang['common_employee_contribution'] = 'Contribution de l&apos;employé';
$lang['common_employer_contribution'] = 'Contribution de l&apos;employeur';
$lang['common_expense_gl_code'] = 'Code GL des dépenses';
$lang['common_expense'] = 'Dépenses';
$lang['common_liability_gl_code'] = 'Code GL des responsabilités';

$lang['common_bank_transfer'] = 'Virement bancaire';
$lang['common_employee_bank'] = 'Banque de l&apos;employé';
$lang['common_transfer_date'] = 'Date du virement';
$lang['common_payment_type'] = 'Type de paiement';
$lang['common_payee_only'] = 'Bénéficiaire uniquement';
$lang['common_bank_transfer_details'] = 'Détails du virement bancaire';
$lang['common_cheque_details'] = 'Détails du chèque';
$lang['common_bank_transfer_details'] = 'Détails du virement bancaire';
$lang['common_account_review'] = 'Révision du compte';
$lang['common_double_entry'] = 'Double entrée';

$lang['common_from_date'] = 'À partir du&nbsp;';
$lang['common_to_date'] = 'Jusqu&apos;au&nbsp;';

$lang['common_emp_no'] = 'N° EMP';
$lang['common_discharge_date'] = 'Date de sortie';
$lang['common_discharge_date'] = 'Date de sortie';
$lang['common_service'] = 'Service';
$lang['common_years'] = 'Années';
$lang['common_months'] = 'Mois';
$lang['common_days'] = 'Jours';
$lang['common_contract_type'] = 'Type de contrat';

$lang['common_emp_first_name'] = 'Prénom';
$lang['common_emp_second_name'] = 'Deuxième prénom';
$lang['common_emp_third_name'] = 'Troisième prénom';
$lang['common_emp_fourth_name'] = 'Quatrième prénom';
$lang['common_emp_family_name'] = 'Nom de famille';

$lang['common_first_name_is_required'] = 'Le prénom est requis';
$lang['common_second_name_is_required'] = 'Le deuxième prénom est requis';
$lang['common_third_name_is_required'] = 'Le troisième prénom est requis';
$lang['common_family_name_is_required'] = 'Le nom de famille est requis';
$lang['emp_secondary_code_is_required'] = 'Le code secondaire est requis';

$lang['common_insurance_no'] = 'Numéro d&apos;assurance';
$lang['common_emergency_contact_details'] = 'Détails du contact d&apos;urgence';
$lang['common_work_contact_details'] = 'Détails du contact professionnel';
$lang['common_contact_person'] = 'Personne à contacter';
$lang['common_customer_weburl'] = 'Site web du client';
$lang['common_payment_terms'] = 'Conditions de paiement';
$lang['common_add_emergency_contact'] = 'Ajouter un contact d&apos;urgence';
$lang['common_contact_number'] = 'Numéro de contact';
$lang['common_primary'] = 'Principal';
$lang['common_other'] = 'Autre';
$lang['common_are_you_sure_you_want_to_make_this_as_default'] = 'Voulez-vous définir cet enregistrement comme défaut ?';
$lang['common_office_no'] = 'Numéro de bureau';
$lang['common_ext'] = 'Ext.';
$lang['common_land_line'] = 'Ligne fixe';
$lang['common_travel_frequency'] = 'Fréquence de voyage';
$lang['common_add_travel_frequency'] = 'Ajouter une fréquence de voyage';
$lang['common_insurance_details'] = 'Détails de l&apos;assurance';
$lang['common_visa_details'] = 'Détails du visa';
$lang['common_passport_details'] = 'Détails du passeport';
$lang['common_not_active'] = 'Inactif';
$lang['common_history'] = 'Historique';
$lang['common_document_upload'] = 'Téléchargement de document';
$lang['common_probation_period'] = 'Période d&apos;essai';
$lang['common_is_open_contract'] = 'Contrat ouvert';
$lang['common_edit_employment_type'] = 'Modifier le type d&apos;emploi';
$lang['common_contract_period'] = 'Période du contrat';
$lang['common_adjustment_type'] = 'Type d&apos;ajustement';
$lang['common_documents_no'] = 'Numéro de document';
$lang['common_ctccost'] = 'Coût';
$lang['common_isCTC'] = 'Coût pour l&apos;entreprise';
$lang['common_other_details'] = 'Autres détails';
$lang['common_header'] = 'En-tête';
$lang['common_no_of_unit'] = 'Nombre d&apos;unités';
$lang['common_variable_pay_declarations'] = 'Déclarations de rémunération variable';
$lang['common_variable_pay_declarations_history'] = 'Historique des déclarations de rémunération variable';
$lang['common_effective_date'] = 'Date effective';
$lang['common_medical_info'] = 'Informations médicales';

$lang['common_invoiced_return'] = 'Facturé / Retourné';
$lang['common_order_total'] = 'Total de la commande';
$lang['common_due'] = 'Dû';
$lang['common_paid'] = 'Payé';
$lang['common_un_billed_invoice'] = 'Facture non facturée';
$lang['common_do_value'] = 'Valeur DO';
$lang['common_arabic'] = 'Arabe';

$lang['common_basic_gross'] = 'Salaire de base / Brut';
$lang['common_annual_leave'] = 'Congé annuel';
$lang['common_no_of_working_days'] = 'Nombre de jours travaillés dans le mois';
$lang['common_leave_pay_formula'] = '<b>Formule :</b> Solde de congé * (Salaire de base ou Brut / Nombre de jours travaillés dans le mois)';
$lang['common_leave_balance'] = 'Solde de congé';
$lang['common_select_location'] = 'Sélectionner un emplacement';
$lang['common_leave_days'] = 'Nombre de jours de congé dans le mois';

$lang['common_grade'] = 'Grade';
$lang['common_no_of_years'] = 'Nombre d&apos;années';
$lang['common_fixed_gross_salary'] = 'Salaire de base';
$lang['common_reporting_currency'] = 'Monnaie de reporting';
$lang['common_local_currency'] = 'Monnaie locale';

$lang['common_salary_advance_request'] = 'Demande d&apos;avance sur salaire';
$lang['common_salary_advance_request_form'] = 'Formulaire de demande d&apos;avance';
$lang['common_salary_declaration_detail'] = 'Détail de la déclaration de salaire';
$lang['common_salary_advance_request_approval'] = 'Approbation de la demande d&apos;avance sur salaire';
$lang['common_is_salary_advance'] = 'Est-ce une avance sur salaire';
$lang['common_salary_advance'] = 'Avance sur salaire';
$lang['common_employer'] = 'Employeur';

$lang['common__bank_or_cash'] = 'Banque ou Espèces';

$lang['common_serial_no'] = 'Numéro de série';
$lang['common_no_message_found'] = 'Aucun message trouvé';
$lang['common_registration_no'] = 'Numéro d&apos;enregistrement';

$lang['common_emp_code'] = 'CODE EMP';
$lang['common_swift_code'] = 'Code Swift';

$lang['common_invoice_no'] = 'Numéro de facture';
$lang['common_invoice_to'] = 'Facturer à';
$lang['common_invoice_items'] = 'Articles de la facture';
$lang['common_invoice_detail'] = 'Détail de la facture';
$lang['common_payment_detail'] = 'Détail du paiement';

$lang['common_you_want_to_proceed'] = 'Voulez-vous continuer';

$lang['common_unpaid'] = 'Non payé';
$lang['common_pending_for_verification'] = 'En attente de vérification';
$lang['common_payment_received_date'] = 'Date de réception du paiement';

$lang['common_standard'] = 'Standard';
$lang['common_increment'] = 'Augmentation';

$lang['common_request_letters'] = 'Demande de document';
$lang['common_letter_type'] = 'Type de lettre';
$lang['common_letter_addressed'] = 'Lettre adressée à';
$lang['common_language'] = 'Langue';

$lang['common_no_of_days'] = 'Nombre de jours';
$lang['common_expired'] = 'Expiré';
$lang['common_dependents'] = 'Personnes à charge';
$lang['common_completed'] = 'Terminé';
$lang['common_not_completed'] = 'Non terminé';

$lang['common_assign_date'] = 'Date d&apos;affectation';
$lang['common_approve'] = 'Approuver';

$lang['common_identity'] = 'Identité';
$lang['common_identity_no'] = 'Numéro d&apos;identité';
$lang['common_signature'] = 'Signature';
$lang['common_signature_is_required'] = 'La signature est requise';
$lang['common_device_id'] = 'ID de l&apos;appareil';
$lang['common_machine_configuration'] = 'Configuration de la machine';
$lang['common_payroll_group'] = 'Groupe de paie';

$lang['common_location_and_date'] = 'Emplacement et date';
$lang['common_location_and_employee'] = 'Emplacement et employé';

$lang['common_select'] = 'Sélectionner';
$lang['common_usage_hours'] = 'Heures d&apos;utilisation';
$lang['common_save_and_complete'] = 'Sauvegarder & Terminer';
$lang['common_start_time'] = 'Heure de début';
$lang['common_end_time'] = 'Heure de fin';
$lang['common_hours_spent'] = 'Heures passées';

$lang['common_customer_category'] = 'Catégorie de client';
$lang['common_document_types'] = 'Types de document';
//$lang['common_document_code'] = 'Code du document';
//$lang['common_document_ty'] = 'Date du document';
$lang['common_date_from'] = 'Date de';
$lang['common_date_to'] = 'Date à';
$lang['common_financial_year'] = 'Exercice financier';

$lang['common_group_by'] = 'Grouper par';
$lang['common_area'] = 'Zone';
$lang['common_type_as'] = 'Type comme';
$lang['common_sub_area'] = 'Sous-zone';
$lang['common_close'] = 'Fermer';

$lang['common_document_header'] = 'EN-TÊTE DU DOCUMENT';
$lang['common_you_want_to_deactivate_this_price'] = 'Vous voulez désactiver ce prix !';

$lang['common_you_want_to_apply_this_to_all'] = 'Vous voulez appliquer ceci à tous';
$lang['common_create'] = 'Créer';
$lang['common_create_new_document'] = 'Créer un nouveau document';
$lang['common_document_tracing'] = 'Suivi du document';
$lang['common_document_edit_all '] = 'Modifier tout';
$lang['common_add_note'] = 'Ajouter une note';
$lang['common_customer_telephone'] = 'Téléphone du client';
$lang['common_customer_email'] = 'Email du client';
$lang['common_discount_details'] = 'Détails de la remise';

$lang['common_mobile_credit_limit'] = 'Limite de crédit mobile';
$lang['common_step_four'] = 'Étape 4';
$lang['common_documents'] = 'Documents';
$lang['common_document_upload'] = 'Téléchargement de document';
$lang['common_discount_amount'] = 'Montant de la remise';
$lang['common_discount_percentagae'] = 'Pourcentage de remise';
$lang['common_discount_total'] = 'Total de la remise';
$lang['common_driver_name'] = 'Nom du conducteur';
$lang['common_vehicle_no'] = 'Numéro de véhicule';
$lang['common_invoice_code'] = 'Code de la facture';
$lang['common_payment_code'] = 'Code de paiement';
$lang['common_payment_date'] = 'Date de paiement';
$lang['common_requested_date'] = 'Date demandée';
$lang['common_requested_by'] = 'Demandé par';
$lang['common_requested_qty'] = 'Quantité demandée';
$lang['common_document_tracing'] = 'Suivi du document';

$lang['common_request_confirmation'] = 'Confirmation de la demande';
$lang['common_request_header'] = 'En-tête de la demande';
$lang['common_request_detail'] = 'Détail de la demande';
$lang['common_balance_qty'] = 'Quantité restante';
$lang['common_previous_year'] = 'Année précédente';
$lang['common_created_by'] = 'Créé par';
$lang['common_created_date'] = 'Date de création';
$lang['common_cap_amount'] = 'Montant plafond';
$lang['common_bulk_upload'] = 'Téléchargement en masse';
$lang['common_add_attributes'] = 'Ajouter des attributs';
$lang['common_outlets'] = 'Points de vente';
$lang['common_origin_documnet_code'] = 'Code du document d&apos;origine';
$lang['common_filter_by_location'] = 'Filtrer par emplacement';
$lang['common_filter_by_segment'] = 'Filtrer par segment';
$lang['common_item_category'] = 'Catégorie d&apos;article';
$lang['common_summary'] = 'Résumé';
$lang['common_payee'] = 'Bénéficiaire';
$lang['common_download'] = 'Télécharger';
$lang['common_confrim_and_submit'] = 'Confirmer & Soumettre';
$lang['common_sub_total'] = 'Sous-total';
$lang['common_compose'] = 'Composer';
$lang['common_back'] = 'Retour';
$lang['common_folders'] = 'Dossiers';
$lang['common_read_mail'] = 'Lire le mail';
$lang['common_reply'] = 'Répondre';
$lang['common_forward'] = 'Transférer';
$lang['common_sent'] = 'Envoyé';
$lang['common_mail_box_configuration'] = 'Configuration de la boîte mail';
$lang['common_account_type'] = 'Type de compte';
$lang['common_email_encryption'] = 'Cryptage des emails';
$lang['common_host'] = 'Hôte';
$lang['common_compose_new_message'] = 'Composer un nouveau message';
$lang['common_Mail_box'] = 'Boîte aux lettres';
$lang['common_message'] = 'Message';
$lang['common_account_name'] = 'Nom du compte';
$lang['common_account_category'] = 'Catégorie de compte';
$lang['common_bank_account_no'] = 'Numéro de compte bancaire';
$lang['common_system_code'] = 'Code système';
$lang['common_item_master'] = 'Master d&apos;article';
$lang['common_replicate'] = 'Répliquer';
$lang['common_item_replicate'] = 'Répliquer l&apos;article';
$lang['common_item_name'] = 'Nom de l&apos;article';
$lang['common_create_category'] = 'Créer une catégorie';
$lang['common_category'] = 'Catégorie';
$lang['common_task'] = 'Tâche';
$lang['common_income'] = 'Revenu';
$lang['common_expence'] = 'Dépense';
$lang['common_essets'] = 'Actifs';
$lang['common_liability'] = 'Passif';
$lang['common_is_default'] = 'Est par défaut';
$lang['common_location_code'] = 'Code d&apos;emplacement';
$lang['common_un_confirm'] = 'Non confirmé';
$lang['common_sub_type'] = 'Sous-type';
$lang['common_to_excel'] = 'Vers Excel';
$lang['common_QHSE_login'] = 'Connexion QHSE';
$lang['common_you_want_to_proceed_with'] = 'Vous voulez continuer avec';

$lang['common_suom'] = 'SUOM';
$lang['common_secondary_qty'] = 'Quantité secondaire';

$lang['common_excel'] = 'Excel';

$lang['common_salary_category'] = 'Catégorie de salaire';
$lang['common_start_range'] = 'Plage de début';
$lang['common_end_range'] = 'Plage de fin';

$lang['common_make_this_primary'] = 'Vous voulez rendre cet enregistrement principal !';

$lang['common_notify_to'] = 'Notifier à';
$lang['common_reset'] = 'Réinitialiser';
$lang['common_no_of_columns'] = 'Nombre de colonnes';
$lang['common_marks'] = 'Notes';
$lang['common_grades'] = 'Grades';
$lang['common_objective'] = 'Objectif';
$lang['common_objectives'] = 'Objectifs';
$lang['common_goal_objectives'] = 'Objectifs de but';
$lang['common_closed_by'] = 'Fermé par';
$lang['common_closed_date'] = 'Date de fermeture';
//$lang['common_weight'] = 'Poids';
$lang['common_last_updated'] = 'Dernière mise à jour';
$lang['common_comment_is_required'] = 'Le commentaire est requis';
$lang['common_remove'] = 'Supprimer';
$lang['common_narration_is_required'] = 'La narration est requise';
$lang['common_date_is_invalid'] = 'La date est invalide';
$lang['common_open'] = 'Ouvrir';
$lang['common_saved_as_draft'] = 'Enregistré en tant que brouillon';
$lang['common_rejected'] = 'Rejeté';
$lang['common_referred_back'] = 'Renvoyé';
$lang['common_add_task'] = 'Ajouter une tâche';

$lang['common_supplier_address'] = 'Adresse du fournisseur';
$lang['common_supplier_telephone'] = 'Téléphone du fournisseur';
$lang['common_authorized_signature'] = 'Signature autorisée';
$lang['common_unit_rate'] = 'Taux unitaire';
$lang['common_approved_details'] = 'Détails approuvés';
$lang['common_closed_details'] = 'Détails fermés';
$lang['common_document_not_closed_yet'] = 'Document non encore fermé';
$lang['common_closed_user'] = 'Utilisateur fermé';
$lang['common_contact_person_is_required'] = 'La personne de contact est requise';
$lang['common_supplier_code'] = 'Code fournisseur';
$lang['common_item_image'] = 'Image de l&apos;article';
$lang['common_total_amount'] = 'Montant total';
$lang['common_project_category'] = 'Catégorie de projet';
$lang['common_project_subcategory'] = 'Sous-catégorie de projet';
$lang['common_invoice_due_date'] = 'Date d&apos;échéance de la facture';
$lang['common_item_status'] = 'Statut de l&apos;article';
$lang['common_unit_price'] = 'Prix unitaire';
$lang['common_net_unit_price'] = 'Prix net unitaire';
$lang['common_not_applicable'] = 'Non applicable';
$lang['common_remarks'] = 'Remarques';
$lang['common_net_total'] = 'Total net';

$lang['common_location'] = 'Emplacement';

$lang['common_customer_systemcode'] = 'Code client';
$lang['common_secondary_code'] = 'Code secondaire';
$lang['common_referenceNo'] = 'Référence';


//Newly Added
$lang['common_templates'] = 'Modèles';

$lang['common_select_provision_gl'] = 'Sélectionner le GL de provision';
$lang['common_select_gl'] = 'Sélectionner le GL';
$lang['common_provision_gl'] = 'GL de provision :';
$lang['common_salary_categories'] = 'Catégories de salaire';
$lang['common_salary_category'] = 'Catégorie de salaire';
$lang['common_leave_salary_provision_cinfiguration'] = 'Configuration de la provision de salaire de congé';

$lang['common_create_amendment'] = 'Créer une modification';
$lang['common_close_amendment'] = 'Fermer la modification';

//inventory catalogue
$lang['common_inventory_catelogue'] = 'Catalogue d&apos;inventaire';
$lang['common_create_inventory_catlogue'] = 'Créer un catalogue d&apos;inventaire';
$lang['common_add_new_inventory_catalogue'] = 'Ajouter un nouveau catalogue d&apos;inventaire';

$lang['common_inventory_catelogue'] = 'Catalogue d&apos;inventaire';
$lang['common_create_inventory_catlogue'] = 'Créer un catalogue d&apos;inventaire';
$lang['common_add_new_inventory_catalogue'] = 'Ajouter un nouveau catalogue d&apos;inventaire';

$lang['common_travel_request'] = 'Demande de voyage/expédition';
$lang['common_travel_request_header'] = 'En-tête de la demande de voyage/expédition';
$lang['common_travel_request_approval'] = 'Approbation de la demande de voyage/expédition';
$lang['common_travel_request_details'] = 'Détails de la demande de voyage/expédition';
$lang['common_travel_request_confirmation'] = 'Confirmation de la demande de voyage/expédition';
$lang['common_travel_request_attachments'] = 'Pièces jointes de la demande de voyage/expédition';
$lang['common_travel_request_number'] = 'Numéro de demande de voyage/expédition';
$lang['common_travel_request_Date'] = 'Date de la demande de voyage/expédition';
$lang['common_add_travel_request_details'] = 'Ajouter des détails de demande de voyage/expédition';
$lang['common_edit_travel_request_details'] = 'Modifier les détails de la demande de voyage/expédition';
$lang['common_create_travel-request'] = 'Créer une demande de voyage/expédition';
$lang['common_edit_travel_request'] = 'Modifier la demande de voyage/expédition';
$lang['common_trip_type'] = 'Sélectionner un type de voyage';
$lang['common_Employeee'] = 'Nom de l&apos;employé';
$lang['common_Employeee_secondary_code'] = 'Code secondaire';
$lang['common_reporting_manager'] = 'Responsable hiérarchique';
$lang['commom_trip_type'] = 'Type de voyage';
$lang['commom_subject'] = 'Sujet';
$lang['commom_description'] = 'Description';
$lang['commom_trip_start_date'] = 'Date de début du voyage';
$lang['commom_trip_end_date'] = 'Date de fin du voyage';
$lang['commom_trip_country'] = 'Pays du voyage';
$lang['commom_Airport_City'] = 'Ville de l&apos;aéroport';
$lang['commom_reason'] = 'Raison du voyage';
$lang['commom_currency'] = 'Devise';
$lang['commom_travel_advance'] = 'Montant';
$lang['commom_destination'] = 'Destination';
$lang['commom_employee_code'] = 'Code employé';
$lang['commom_trip'] = 'Type de voyage';
$lang['commom_Airport'] = 'Aéroport';

$lang['common_retension'] = 'Rétention';
$lang['common_commission'] = 'Commission';
$lang['common_taxapplicable'] = 'Taxe applicable';

$lang['common_stage'] = 'Étapes';
$lang['common_DefaultType'] = 'Type par défaut';

$lang['common_you_want_to_pull_extra_charges'] = 'Vous voulez récupérer les frais supplémentaires par défaut !';

$lang['common_DefaultType'] = 'Type par défaut';
$lang['common_weightage'] = 'Poids';
$lang['common_checklist'] = 'Liste de contrôle';
$lang['common_checklist_description'] = 'Description de la liste de contrôle';
$lang['common_actual_hours_spent'] = 'Heures réelles passées';

$lang['common_link_customer'] = 'Client lié';


//start : Ray company Document policy (newly added)
if ($ray_Company_document_policy == 1)
{
    $lang['common_supplier_document_date'] = 'Date d&apos;application';
    $lang['common_supplier_invoice_date'] = 'Date de la facture du fournisseur';
}
else
{
    $lang['common_supplier_document_date'] = 'Date du document';
    $lang['common_supplier_invoice_date'] = 'Date de la facture';
}
//end ; 

$lang['common_is_inter_company'] = 'Est-ce une société inter';
$lang['common_inter_company'] = 'Société inter';

$lang['common_travel_Request_Approval'] = 'Approbation de la demande de voyage/expédition';
$lang['common_travel_Request_status_required'] = 'Le statut est requis.';
$lang['common_travel_Request_order_status_is_required'] = 'Le statut de la commande du niveau est requis.';
$lang['common_travel_Request_id_is_required'] = 'L&apos;ID de la demande de voyage/expédition est requis.';
$lang['common_travel_request'] = 'Demande de voyage';
$lang['common_travel_request_approvals_reject_process_successfully_done'] = 'Le processus de rejet des approbations a été effectué avec succès';
$lang['common_travel_request_previous_level_approval_not_finished'] = 'L&apos;approbation du niveau précédent n&apos;est pas terminée';
$lang['common_travel_request_error_in_paysheet_approvals_of'] = 'Erreur dans les approbations de la demande de voyage/expédition de';

$lang['common_you_want_to_generate'] = 'Vous voulez générer !';
$lang['commom_local_mobile'] = 'Numéro de mobile local';
$lang['commom_seat_preference'] = 'Préférence de siège';
$lang['commom_meal_preference'] = 'Préférence de repas';
$lang['commom_flyer_no_if_any'] = 'Numéro de voyageur fréquent, si disponible';
$lang['common_family_name'] = 'Nom de famille';
$lang['common_emirates_details'] = 'Détails de l&apos;Emirates';
$lang['common_emirates_no'] = 'ID Emirates';
$lang['emp_emirate_expiry_date'] = 'Date d&apos;expiration de l&apos;Emirates';
$lang['common_Accrual'] = 'JV de provision';
$lang['common_reporting_changes'] = 'Modifications du responsable hiérarchique';
$lang['common_is_primary'] = 'Est-ce primaire';
$lang['common_department_changes'] = 'Modifications du département';
$lang['common_bank_changes'] = 'Modifications des coordonnées bancaires';
$lang['common_air_ticket_enhancemnet'] = 'Encaissement de billet d&apos;avion';

$lang['common_Loan'] = 'Prêt';
$lang['common__pending_Loan'] = 'Prêts en attente';
$lang['Loan_No'] = 'Numéro du prêt';
$lang['common_loan_amount'] = 'Montant du prêt';
$lang['common_total_intallmanets'] = 'Total des paiements';
$lang['common_pending_intallmanets'] = 'Paiements en attente';
$lang['common_loan_details'] = 'Détails du prêt';

  
// OT Summary
$lang['common_ot_summary'] = 'Résumé des heures supplémentaires';
$lang['common_ot_summary_day_wise'] = 'Résumé des heures supplémentaires par jour';
$lang['common_total_hours'] = 'Heures totales';
$lang['common_ot_type'] = 'Type d&apos;heures supplémentaires';
$lang['common_normal_ot'] = 'Heures supplémentaires jour normal';
$lang['common_weekend_ot'] = 'Heures supplémentaires week-end';
$lang['common_holiday_ot'] = 'Heures supplémentaires jour férié';

$lang['common_master_segment'] = 'Segment principal';
$lang['common__pending_Loan_detail'] = 'Détails des prêts en attente';

$lang['common_interCompnay'] = 'Inter-Société';
$lang['common_reserved'] = 'Réservé';
$lang['common_previous_month'] = 'Mois précédent';
$lang['common_previous_reporting_currency'] = 'Devise de reporting du mois précédent';
$lang['common_previous_local_currency'] = 'Devise locale du mois précédent';
$lang['common_open_leave'] = 'Congé ouvert';

$lang['common_purchase_history'] = 'Historique des achats';
$lang['common_doc_type'] = 'Type de document';
$lang['common_doc_number'] = 'Numéro de document';
$lang['common_transection_qty'] = 'Quantité de la transaction';
$lang['common_transection_currency'] = 'Montant de la transaction';

$lang['common_accomodation'] = 'Hébergement';
$lang['common_accomodation_type'] = 'Type d&apos;hébergement';
$lang['common_accomodation_add'] = 'Ajouter un hébergement';
$lang['common_add_accomodation_to_employee'] = 'Assigner l&apos;hébergement';

$lang['common_link_leave'] = 'Lier un congé';
$lang['common_trip_request_type'] = 'Type de demande de voyage';
$lang['common_project_type'] = 'Type de projet';
$lang['common_project'] = 'Projet';
$lang['commom_class_type'] = 'Type de classe';
$lang['commom_economic_class'] = 'Classe économique';
$lang['commom_business_lass'] = 'Classe affaires';
$lang['commom_birthdate'] = 'Date de naissance';
$lang['commom_from_destination'] = 'Destination de départ';
$lang['commom_to_destination'] = 'Destination d&apos;arrivée';
$lang['common_get_travel_request'] = 'Obtenir la demande de voyage';
$lang['common_add_travel_type'] = 'Ajouter un type de voyage';
$lang['common_travel_type_master'] = 'Type de voyage - Master';
$lang['common_travel_type'] = 'Type de voyage';
$lang['common_mid_range'] = 'Plage moyenne';
$lang['common_max_val'] = 'Valeur maximale';
$lang['common_special_user'] = 'Utilisateur spécial';
$lang['common_add_special_User'] = 'Ajouter un utilisateur spécial';
$lang['common_Div-Country_Initials_S.No_Year']='(Div-Pays / Initiales / N° S. / Année)';
$lang['common_newbooking']='NOUVELLE RÉSERVATION';
$lang['common_OTHERS_pls']='AUTRES (svp préciser)';
$lang['common_office_staff']='POUR LE PERSONNEL DU BUREAU';
$lang['common_filed_staff']='POUR LE PERSONNEL DE TERRAIN';
$lang['common_focal_person']='Personne de contact';
$lang['common_traveller_information']='INFORMATIONS SUR LE VOYAGEUR (tel qu&apos;indiqué sur le passeport)';
$lang['common_middle_name']='Deuxième prénom';
$lang['common_last_name']='Nom de famille';
$lang['common_purpose_of_travel']='But du voyage';
$lang['common_code_div_territory']="Code Div./Territoire";
$lang['common_expense_code']="Code des dépenses";
$lang['common_project_code']="Code du projet";
$lang['common_age']="Âge";
$lang['common_type_of_travel']="Type de voyage";
$lang['common_one_way']="Aller simple";
$lang['common_return']="Retour";
$lang['common_class']="Classe";
$lang['common_economy']="Économie";
$lang['common_business']="Affaires";
$lang['common_business_trip_approved']="Voyage d'affaires approuvé par le CEO";
$lang['common_Na']='N/A';
$lang['common_approval_Type']='Type d&apos;approbation';
$lang['common_leave_approve']="Congé approuvé";
$lang['common_trip_approve']="Voyage approuvé";
$lang['common_family_travel']="Voyage en famille";
$lang['common_itinerary_required']="ITINÉRAIRE DE VOYAGE REQUIS";
$lang['common_departure_date']="Date de départ";
$lang['common_sector']="Secteur";
$lang['common_return_date']="Date de retour";
$lang['common_overseas_mob']="Numéro de mobile à l&apos;étranger";
$lang['common_do_you_have_travel_visa']="Avez-vous un visa de voyage ?";
$lang['commom_LPO_to_be_filled']="LPO - À REMPLIR PAR LE POINT DE CONTACT";
$lang['commom_LPO_no']="Numéro LPO (abréviation de l'agent de voyage / N° TRF)";
$lang['commom_airline']="Compagnie aérienne";
$lang['common_travel_date']="Date de voyage";
$lang['common_ticket_no']="Numéro de billet";
$lang['common_base_fare']="Tarif de base";
$lang['common_taxes']="Taxes";
$lang['common_currency_total']="Devise/Total";
$lang['common_to_be_filled_by_MSE']="À remplir par le point de contact MSE Travel, si applicable";
$lang['common_total_in_words']="Total en mots";
$lang['common_add_more_details']="Ajouter plus de détails";
$lang['common_depature_details']="Détails du départ";
$lang['common_return_details']="Détails du retour";
$lang['common_do_you_have_visa']="Avez-vous un visa ?";
$lang['common_booking_type']="Type de réservation";
$lang['common_new_booking']="Nouvelle réservation";
$lang['common_others_pls']="AUTRES (svp préciser)";
$lang['common_office_staff_simple']='Pour le personnel du bureau';
$lang['common_expiry_date']='Date d&apos;expiration';