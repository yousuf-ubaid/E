<?php

/**
 * System messages translation for CodeIgniter(tm)
 *
 * @prefix : assetmanagement_
 */
defined('BASEPATH') OR exit('No direct script access allowed');

$LanguagePolicy = getPolicyValues('LNG', 'All');

$lang['manufacturing_select_image'] = 'Sélectionner une image';
$lang['manufacturing_change'] = 'Changer';
$lang['manufacturing_remove'] = 'Supprimer';
$lang['manufacturing_new_overhead'] = 'Nouveau surcoût';
$lang['manufacturing_add_crew'] = 'Ajouter une équipe';
$lang['manufacturing_add_customer'] = 'Ajouter un client';
$lang['manufacturing_add_segment'] = 'Ajouter un segment';
$lang['manufacturing_excel'] = 'Excel';
$lang['manufacturing_add_new_status'] = 'Ajouter un nouveau statut';
$lang['manufacturing_add_new_user'] = 'Ajouter un nouvel utilisateur';
$lang['manufacturing_new_labour'] = 'Nouveau travail';
$lang['manufacturing_add_labour'] = 'Ajouter un travail';
$lang['manufacturing_add_warehouse'] = 'Ajouter un entrepôt';
$lang['manufacturing_save_warehouse'] = 'Enregistrer l&apos;entrepôt';
$lang['manufacturing_add_user_group'] = 'Ajouter un groupe d&apos;utilisateurs';
$lang['manufacturing_edit_user_group'] = 'Modifier le groupe d&apos;utilisateurs';
$lang['manufacturing_add_employee'] = 'Ajouter un employé';
$lang['manufacturing_add_standard_details'] = 'Ajouter des détails standard';
$lang['manufacturing_edit_standard_details'] = 'Modifier les détails standard';
$lang['manufacturing_send_email'] = 'Envoyer un e-mail';
$lang['manufacturing_add_machine'] = 'Ajouter une machine';
$lang['manufacturing_rfi_detial'] = 'Détails RFI';

$lang['manufacturing_job_no'] = 'Numéro de travail';
$lang['manufacturing_job_number'] = 'Numéro de travail';
$lang['manufacturing_job_id'] = 'ID de travail';
$lang['manufacturing_job_card'] = 'Carte de travail';
$lang['manufacturing_job_approval'] = 'Approbation du travail';
$lang['manufacturing_job_date'] = 'Date du travail';
$lang['manufacturing_jobs'] = 'Travaux';
$lang['manufacturing_job'] = 'Travail';
$lang['manufacturing_generate_job'] = 'Générer un travail';
$lang['manufacturing_job_attachment'] = 'Pièce jointe de travail';
$lang['manufacturing_document_date'] = 'Date du document';
$lang['manufacturing_customer'] = 'Client';
$lang['manufacturing_item'] = 'Article';
$lang['manufacturing_description'] = 'Description';
$lang['manufacturing_job_status'] = 'Statut du travail';
$lang['manufacturing_status'] = 'Statut';
$lang['manufacturing_percentage'] = 'Pourcentage';

$lang['manufacturing_new_bill_of_material'] = 'Nouveau devis de matériel';
$lang['manufacturing_bom_code'] = 'Code du devis';
$lang['manufacturing_product_name'] = 'Nom du produit';
$lang['manufacturing_industry_type'] = 'Type d&apos;industrie';

$lang['manufacturing_bom_information'] = 'Informations sur le devis';
$lang['manufacturing_product'] = 'Produit';
$lang['manufacturing_date'] = 'Date';
$lang['manufacturing_unit_of_measure'] = 'Unité de mesure';
$lang['manufacturing_quantity'] = 'Quantité';
$lang['manufacturing_currency'] = 'Devise';

$lang['manufacturing_material_consumption'] = 'Consommation de matériel';
$lang['manufacturing_part_no'] = 'Numéro de pièce';
$lang['manufacturing_unit_of_measure_short'] = 'UoM';
$lang['manufacturing_quantity_required'] = 'Quantité requise';
$lang['manufacturing_cost_type'] = 'Type de coût';
$lang['manufacturing_material_cost'] = 'Coût du matériel';
$lang['manufacturing_standard_loss'] = 'Perte standard %';
$lang['manufacturing_material_change'] = 'Changement de matériel';
$lang['manufacturing_material_totals'] = 'Totaux du matériel';

$lang['manufacturing_labour_tasks'] = 'Tâches de travail';
$lang['manufacturing_activity_code'] = 'Code d&apos;activité';
$lang['manufacturing_department'] = 'Département';
$lang['manufacturing_unit_rate'] = 'Taux unitaire';
if($LanguagePolicy == 'FlowServe'){
    $lang['manufacturing_total_hours'] = 'Heures estimées';
}else{
    $lang['manufacturing_total_hours'] = 'Total des heures';
}

$lang['manufacturing_total_value'] = 'Valeur totale';
$lang['manufacturing_labour_totals'] = 'Totaux de travail';

$lang['manufacturing_overhead_cost'] = 'Coût de surcoût';
$lang['manufacturing_overhead_totals'] = 'Totaux de surcoût';
$lang['manufacturing_machine'] = 'Machine';
$lang['manufacturing_machine_id'] = 'ID de la machine';
$lang['manufacturing_machine_totals'] = 'Totaux des machines';
$lang['manufacturing_total_cost'] = 'Coût total :';
$lang['manufacturing_cost_per_unit'] = 'Coût par unité :';
$lang['manufacturing_quantity_used'] = 'Quantité utilisée';

$lang['manufacturing_item_information'] = 'Informations sur l&apos;article';
$lang['manufacturing_finance_category'] = 'Catégorie financière';
$lang['manufacturing_sub_category'] = 'Sous-catégorie';
$lang['manufacturing_sub_sub_category'] = 'Sous-sous-catégorie';
$lang['manufacturing_secondary_code'] = 'Code secondaire';
$lang['manufacturing_industry'] = 'Industrie';

$lang['manufacturing_estimated_qty'] = 'Quantité estimée';
$lang['manufacturing_usage_qty'] = 'Quantité utilisée';
$lang['manufacturing_loss'] = 'Perte%';
$lang['manufacturing_mark_up'] = 'Marge%';

$lang['manufacturing_add_overhead'] = 'Ajouter frais généraux';
$lang['manufacturing_workflow_category'] = 'Catégorie de workflow';
$lang['manufacturing_page_link'] = 'Lien de la page';
$lang['manufacturing_workflow_template'] = 'Modèle de workflow';

$lang['manufacturing_linked_to_erp'] = 'Lié à l&apos;ERP';
$lang['manufacturing_crews_from_erp'] = 'Équipes de l&apos;ERP';
$lang['manufacturing_crew_detail'] = 'Détail de l&apos;équipe';
$lang['manufacturing_customer_detail'] = 'Détail du client';
$lang['manufacturing_contact_detail'] = 'Détail du contact';
$lang['manufacturing_item_name'] = 'Nom de l&apos;article';
$lang['manufacturing_item_code'] = 'Code de l&apos;article';
$lang['manufacturing_categories'] = 'Catégories';
$lang['manufacturing_main'] = 'Principal';
$lang['manufacturing_sub'] = 'Sous';
$lang['manufacturing_sub_sub'] = 'Sous-sous';

$lang['manufacturing_segment_ID'] = 'ID du segment';
$lang['manufacturing_segment_description'] = 'Description du segment';
$lang['manufacturing_segment_linked_to_erp'] = 'Segment lié à l&apos;ERP';
$lang['manufacturing_segment_detail'] = 'Détail du segment';
$lang['manufacturing_segment_code'] = 'Code du segment';

$lang['manufacturing_select_a_work_flow'] = 'Sélectionner un workflow';
$lang['manufacturing_main_category'] = 'Catégorie principale';
$lang['manufacturing_select_category'] = 'Sélectionner une catégorie';
$lang['manufacturing_select_item_type'] = 'Sélectionner le type d&apos;article';
$lang['manufacturing_select_an_industry'] = 'Sélectionner une industrie';
$lang['manufacturing_select_bom'] = 'Sélectionner le BOM';
$lang['manufacturing_select_order'] = 'Sélectionner la commande';

$lang['manufacturing_step_1'] = 'Étape 1 - ';
$lang['manufacturing_step_2'] = 'Étape 2 - ';
$lang['manufacturing_is_default'] = 'Est par défaut';
                           /*TITLES*/
$lang['manufacturing_customer_master'] = 'MAÎTRE CLIENT';
$lang['manufacturing_crew_master'] = 'MAÎTRE ÉQUIPE';
$lang['manufacturing_segments'] = 'SEGMENTS';
$lang['manufacturing_workflow_process_setup'] = 'CONFIGURATION DU PROCESSUS DE WORKFLOW';
$lang['manufacturing_system_settings'] = 'PARAMÈTRES DU SYSTÈME';
$lang['manufacturing_item_category'] = 'CATÉGORIE D&apos;ARTICLE MFQ';
$lang['manufacturing_labour'] = 'Main-d&apos;œuvre';
$lang['manufacturing_warehouse'] = 'ENTREPÔT';
$lang['manufacturing_user_groups'] = 'GROUPES D&apos;UTILISATEURS';
$lang['manufacturing_estimate'] = 'ESTIMATION';
$lang['manufacturing_customer_inquiry'] = 'ENQUÊTE CLIENT';
$lang['manufacturing_manage_warehouse'] = 'Gérer l&apos;entrepôt';
$lang['manufacturing_manage_segment'] = 'Gérer le segment';
$lang['manufacturing_workflow_header'] = 'En-tête du workflow';
$lang['manufacturing_workflow_configuration'] = 'Configuration du workflow';
$lang['manufacturing_workflow_detail'] = 'Détail du workflow';
$lang['manufacturing_workflow_design'] = 'Conception du workflow';
$lang['manufacturing_standard_job_card'] = 'Carte de travail standard';
$lang['manufacturing_asset_category'] = 'CATÉGORIE D&apos;ACTIF MFQ';

$lang['manufacturing_job'] = 'Travail';
$lang['manufacturing_machine'] = 'Machine';
$lang['manufacturing_review'] = 'Revoir';
$lang['manufacturing_print'] = 'Imprimer';
$lang['manufacturing_review_or_print'] = 'Revoir / Imprimer';
$lang['manufacturing_header'] = 'En-tête';
$lang['manufacturing_card'] = 'Carte';
$lang['manufacturing_bom'] = 'BOM';
$lang['manufacturing_quotation_reference_id'] = 'ID de référence de la citation';
$lang['manufacturing_quality_assurance'] = 'Assurance qualité';
$lang['manufacturing_quality_assurance_criteria'] = 'Critères d&apos;assurance qualité';
$lang['manufacturing_specification'] = 'Spécification';

$lang['manufacturing_quote_reference'] = 'Réf. devis';
$lang['manufacturing_item_detail'] = 'Détail de l&apos;article';
$lang['manufacturing_item_code'] = 'Code de l&apos;article';
$lang['manufacturing_machine_category'] = 'Catégorie de machine';
$lang['manufacturing_asset_code'] = 'Code de l&apos;actif';

$lang['manufacturing_dashboard'] = 'Tableau de bord';
$lang['manufacturing_production_calendar'] = 'Calendrier de production';
$lang['manufacturing_ongoing_job'] = 'Travail en cours';
$lang['manufacturing_close_date'] = 'Date de clôture';
$lang['manufacturing_day_scale'] = 'Échelle journalière';
$lang['manufacturing_week_scale'] = 'Échelle hebdomadaire';
$lang['manufacturing_month_scale'] = 'Échelle mensuelle';
$lang['manufacturing_division'] = 'Division';
$lang['manufacturing_job_description'] = 'Description du travail';
$lang['manufacturing_client_name'] = 'Nom du client';
$lang['manufacturing_job_completion'] = 'Achèvement du travail';
$lang['manufacturing_document_status'] = 'Statut du document';
$lang['manufacturing_background_color'] = 'Couleur de fond';
$lang['manufacturing_text_color'] = 'Couleur du texte';
$lang['manufacturing_status_color'] = 'Couleur du statut';
$lang['manufacturing_finance_date'] = 'Date financière';

$lang['manufacturing_gl_description'] = 'Description GL';
$lang['manufacturing_link_description'] = 'Description du lien';
$lang['manufacturing_warehouse_from_erp'] = 'Entrepôt depuis ERP';
$lang['manufacturing_warehouse_detail'] = 'Détail de l&apos;entrepôt';

$lang['manufacturing_group_type'] = 'Type de groupe';
$lang['manufacturing_is_active'] = 'Est actif';
$lang['manufacturing_is_default'] = 'Est par défaut';
$lang['manufacturing_employee'] = 'Employé';
$lang['manufacturing_employee_name'] = 'Nom de l&apos;employé';
$lang['manufacturing_added_employee'] = 'Employé ajouté';
$lang['manufacturing_standard_details'] = 'Détails standard';
$lang['manufacturing_stock_insufficient'] = 'Stock insuffisant';
$lang['manufacturing_current_stock'] = 'Stock actuel';

$lang['manufacturing_customer_inquiry_approval'] = 'Approbation de la demande client';
$lang['manufacturing_customer_inquiry_attachment'] = 'Pièce jointe de la demande client';
$lang['manufacturing_estimate_approval'] = 'Approbation de l&apos;estimation';
$lang['manufacturing_estimate_attachment'] = 'Pièce jointe de l&apos;estimation';
$lang['manufacturing_standard_job_card_approval'] = 'Approbation de la carte de travail standard';
$lang['manufacturing_standard_job_card_attachment'] = 'Pièce jointe de la carte de travail standard';

$lang['manufacturing_batch_number'] = 'Numéro de lot';
$lang['manufacturing_production_date'] = 'Date de production';
$lang['manufacturing_created_date'] = 'Date de création';
$lang['manufacturing_input'] = 'Entrée';
$lang['manufacturing_raw_material'] = 'Matière première';
$lang['manufacturing_total_amount'] = 'Montant total';
$lang['manufacturing_overhead'] = 'Frais généraux';
$lang['manufacturing_total_input'] = 'Total des entrées :';
$lang['manufacturing_output'] = 'Production';
$lang['manufacturing_finish_goods'] = 'Produits finis';
$lang['manufacturing_total_output'] = 'Total de la production';

$lang['manufacturing_new_customer_inquiry'] = 'Nouvelle demande client';
$lang['manufacturing_inquiry_date'] = 'Date de la demande';
$lang['manufacturing_client'] = 'Client';
$lang['manufacturing_proposal_engineer'] = 'Ingénieur de proposition';
$lang['manufacturing_client_ref_no'] = 'Référence client No';
$lang['manufacturing_actual_submission_date'] = 'Date de soumission réelle';
$lang['manufacturing_planned_submission_date'] = 'Date de soumission planifiée';
$lang['manufacturing_inquiry_status'] = 'Statut de la demande';
$lang['manufacturing_quote_status'] = 'Statut de l&apos;offre';
$lang['manufacturing_customer_inquiry_simple'] = 'Demande client';

$lang['manufacturing_customer_inquiry_information'] = 'Informations sur la demande client';
$lang['manufacturing_contact_person_name'] = 'Nom de la personne à contacter';
$lang['manufacturing_contact_phone_number'] = 'Numéro de téléphone du contact';
$lang['manufacturing_type'] = 'Type de fabrication';
$lang['manufacturing_required_submission_date'] = 'Date de soumission requise';
$lang['manufacturing_delay_in_days'] = 'Délai en jours';
$lang['manufacturing_in_days'] = 'En jours';
$lang['manufacturing_send_reminder_email'] = 'Envoyer un email de rappel';
$lang['manufacturing_client_reference_no'] = 'Numéro de référence client';
$lang['manufacturing_customer_email'] = 'Email du client';
$lang['manufacturing_inquiry_type'] = 'Type de demande';
$lang['manufacturing_engineering'] = 'Ingénierie';
$lang['manufacturing_responsible'] = 'Responsable';
$lang['manufacturing_required_date'] = 'Date requise';
$lang['manufacturing_submission_date'] = 'Date de soumission';
$lang['manufacturing_purchasing'] = 'Achats';
$lang['manufacturing_production'] = 'Production';
$lang['manufacturing_quality_assurance_or_quality_control'] = 'QA/QC';
$lang['manufacturing_expected_quantity'] = 'Quantité attendue';
$lang['manufacturing_delivery_date'] = 'Date de livraison';
$lang['manufacturing_delivery_terms'] = 'Conditions de livraison';
$lang['manufacturing_remarks'] = 'Remarques';
$lang['manufacturing_inquiry_code'] = 'Code de la demande';
$lang['manufacturing_client_reference_no'] = 'Référence client No';
$lang['manufacturing_phone_no'] = 'Numéro de téléphone';
$lang['manufacturing_validity'] = 'Validité';
$lang['manufacturing_terms_and_condition'] = 'Termes et conditions';
$lang['manufacturing_payment_terms'] = 'Conditions de paiement';
$lang['manufacturing_exclusions'] = 'Exclusions';
$lang['manufacturing_technical_detail'] = 'Détail technique';
$lang['manufacturing_scope_of_work'] = 'Portée des travaux';
$lang['manufacturing_warranty'] = 'Garantie';
$lang['manufacturing_approval_status'] = 'Statut de l&apos;approbation';

$lang['manufacturing_estimation'] = 'Estimation';
$lang['manufacturing_estimate_date'] = 'Date de l&apos;estimation';
$lang['manufacturing_estimate_information'] = 'Informations sur l&apos;estimation';
$lang['manufacturing_estimate_information'] = 'En-tête de l&apos;estimation';
$lang['manufacturing_estimate_detail'] = 'Détail de l&apos;estimation';
$lang['manufacturing_new_estimate'] = 'Nouvelle estimation';
$lang['manufacturing_estimate_code'] = 'Code de l&apos;estimation';
$lang['manufacturing_estimate_status'] = 'Statut de l&apos;estimation';

$lang['manufacturing_tender_SLno'] = 'No. SL';
$lang['manufacturing_tender_tender_no'] = 'Numéro d&apos;appel d&apos;offres';
$lang['manufacturing_tender_client'] = 'Client';
$lang['manufacturing_tender_description'] = 'Description';
$lang['manufacturing_tender_category'] = 'Catégorie';
$lang['manufacturing_tender_price'] = 'Prix';
$lang['manufacturing_tender_rfq_type'] = 'Type RFQ';
$lang['manufacturing_tender_micoda_operation'] = 'Opération Micoda';
$lang['manufacturing_tender_rfq_originator'] = 'Origine RFQ';
$lang['manufacturing_tender_source'] = 'Source';
$lang['manufacturing_tender_Estimator'] = 'Estimateur';
$lang['manufacturing_tender_month'] = 'Mois';
$lang['manufacturing_tender_year'] = 'Année';
$lang['manufacturing_tender_rfq_status'] = 'Statut RFQ';
$lang['manufacturing_tender_status'] = 'Statut';
$lang['manufacturing_tender_order_status'] = 'Statut de la commande';
$lang['manufacturing_tender_assigned_date'] = 'Date attribuée';
$lang['manufacturing_tender_submission_date'] = 'Date de soumission';
$lang['manufacturing_tender_actual_submission_date'] = 'Date de soumission réelle';
$lang['manufacturing_tender_submission_status'] = 'Statut de la soumission';
$lang['manufacturing_tender_alloted_manhours'] = 'Heures-homme attribuées';
$lang['manufacturing_tender_actual_manhours'] = 'Heures-homme réelles';
$lang['manufacturing_tender_no_of_days_delayed'] = 'Nombre de jours de retard';
$lang['manufacturing_tender_total'] = 'Total';
$lang['manufacturing_tender_rev'] = 'Rév.';
$lang['manufacturing_tender_po_received_date'] = 'Date de réception du PO';
$lang['manufacturing_tender_po_number'] = 'Numéro du PO';
$lang['manufacturing_tender_project_number'] = 'Numéro de projet';
$lang['manufacturing_tender_remark'] = 'Remarques';

$lang['manufacturing_tender_header'] = 'JOURNAUX D&apos;APPELS D&apos;OFFRES';

$lang['manufacturing_overall_progress_header'] = 'PROGRÈS GLOBAL DU PROJET';

$lang['manufacturing_overall_progress_mic_no'] = 'Numéro MIC';
$lang['manufacturing_overall_progress_tender_no'] = 'Numéro de l&apos;appel d&apos;offres';
$lang['manufacturing_overall_progress_estimate_no'] = 'Numéro de l&apos;estimation';
$lang['manufacturing_overall_progress_job_num'] = 'Numéro du travail';
$lang['manufacturing_overall_progress_client'] = 'CLIENT';
$lang['manufacturing_overall_progress_category'] = 'CATÉGORIE';
$lang['manufacturing_overall_progress_client_po_ref'] = 'Référence PO CLIENT';
$lang['manufacturing_overall_progress_project_focal'] = 'Focal du projet';
$lang['manufacturing_overall_progress_po_value'] = 'Valeur du PO';
$lang['manufacturing_overall_progress_delivery'] = 'LIVRAISON DU PO / IJOF';
$lang['manufacturing_overall_progress_committed_date'] = 'DATE DE COMPLÉTION ENGAGÉE';
$lang['manufacturing_overall_progress_actual_date'] = 'DATE DE COMPLÉTION RÉELLE';
$lang['manufacturing_overall_progress_month'] = 'MOIS';
$lang['manufacturing_overall_progress_year'] = 'ANNÉE';
$lang['manufacturing_overall_progress_des'] = 'Description';
$lang['manufacturing_overall_progress_c_status'] = 'Statut actuel';
$lang['manufacturing_overall_progress_engg'] = 'INGÉNIERIE';
$lang['manufacturing_overall_progress_remark'] = 'REMARQUE';
$lang['manufacturing_overall_progress_PR'] = 'PR';
$lang['manufacturing_overall_progress_re2'] = 'REMARQUE2';
$lang['manufacturing_overall_progress_po'] = 'PO';
$lang['manufacturing_overall_progress_re3'] = 'REMARQUE3';
$lang['manufacturing_overall_progress_fab'] = 'FAB';
$lang['manufacturing_overall_progress_nde'] = 'NDE';
$lang['manufacturing_overall_progress_hydro'] = 'HYDRO';
$lang['manufacturing_overall_progress_paint'] = 'PEINTURE';
$lang['manufacturing_overall_progress_fat'] = 'FAT';
$lang['manufacturing_overall_progress_re4'] = 'REMARQUE4';
$lang['manufacturing_overall_progress_mrb'] = 'MRB';
$lang['manufacturing_overall_progress_pl'] = 'P&L';
$lang['manufacturing_overall_progress_over_pro'] = 'Progrès global atteint (%)';
$lang['manufacturing_overall_progress_total'] = 'TOTAL';
$lang['manufacturing_overall_progress_project_with'] = 'PROJET AVEC VARIATION';
$lang['manufacturing_overall_progress_va_amount'] = 'MONTANT DE LA VARIATION';
$lang['manufacturing_overall_progress_status_variation'] = 'STATUT DE LA VARIATION PO';
$lang['manufacturing_overall_progress_estimate_pl'] = 'P&L ESTIMÉ';
$lang['manufacturing_overall_progress_pL'] = 'RÉSULTAT P&L';
$lang['manufacturing_overall_progress_delivery_note'] = 'BON DE LIVRAISON';
$lang['manufacturing_overall_progress_clo_goods'] = 'COLLECTE DES BIENS';

$lang['manufacturing_balance_qty'] = 'Quantité restante';
$lang['manufacturing_total_qty'] = 'Quantité totale';
$lang['manufacturing_cost_detail'] = 'Détail des coûts';
$lang['manufacturing_margin'] = 'Marge';
$lang['manufacturing_discount'] = 'Remise';
$lang['manufacturing_discount_price'] = 'Prix après remise';
$lang['manufacturing_selling_price'] = 'Prix de vente';
$lang['manufacturing_unit_price'] = 'Prix unitaire';
$lang['manufacturing_discounted_amount'] = 'Montant avec remise';

$lang['manufacturing_revisions'] = 'Révisions';
$lang['manufacturing_additional_order_detail'] = 'Détail de la commande supplémentaire';
$lang['manufacturing_master_category'] = 'Catégorie principale';
$lang['manufacturing_add_sub_category'] = 'Ajouter une sous-catégorie';
$lang['manufacturing_category_description'] = 'Description de la catégorie';
$lang['manufacturing_edit_sub_category'] = 'Modifier la sous-catégorie';
$lang['manufacturing_customer_invoice'] = 'FACTURE CLIENT';
$lang['manufacturing_new_customer_invoice'] = 'Nouvelle facture client';
$lang['manufacturing_invoice_code'] = 'Code de la facture';
$lang['manufacturing_due_date'] = 'Date d&apos;échéance';
$lang['manufacturing_serial_no'] = 'Numéro de série';
$lang['manufacturing_contract'] = 'Contrat';
$lang['manufacturing_purchase_order_number'] = 'Numéro de commande PO';
$lang['manufacturing_customer_invoice_simple'] = 'Facture client';
$lang['manufacturing_customer_invoice_information'] = 'Informations sur la facture client';

$lang['manufacturing_gl_detail'] = 'Détail GL';
$lang['manufacturing_item_detail'] = 'Détail de l&apos;article';
$lang['manufacturing_delivery_note'] = 'Bon de livraison';
$lang['manufacturing_invoice_due_date'] = 'Date d&apos;échéance de la facture';
$lang['manufacturing_delivery_note_code'] = 'Code de bon de livraison';
$lang['manufacturing_delivery_note_number'] = 'Numéro de bon de livraison';

$lang['manufacturing_purchase_order_reference'] = 'Réf. de la commande PO';
$lang['manufacturing_description_or_particulars'] = 'Descriptions / Détails';
$lang['manufacturing_vehicle_no'] = 'Numéro du véhicule';
$lang['manufacturing_mobile_no'] = 'Numéro de mobile';
$lang['manufacturing_certifies_that_the_above_mentioned_materials_have_been_received_in_good_order_and_condition_or_as_per_scope_of_work'] = 'Certifie que les matériaux mentionnés ci-dessus ont été reçus en bon état et en conformité avec l&apos;étendue du travail';
$lang['manufacturing_signed_for_hemt_stores'] = 'Signé pour HEMT STORES';
$lang['manufacturing_customer_signature_and_stamp_after_completion_or_receipt'] = 'Signature et tampon du client après achèvement / réception';
$lang['manufacturing_step_one_delivery_note_header'] = 'Étape 1 - En-tête de bon de livraison';
$lang['manufacturing_step_two_delivery_note_confirmation'] = 'Étape 2 - Confirmation du bon de livraison';

$lang['manufacturing_customer_name'] = 'Nom du client';
$lang['manufacturing_driver_name'] = 'Nom du conducteur';
$lang['manufacturing_bill_of_material_head'] = 'BOM (Bill of Material)';
$lang['manufacturing_add_new_bill_of_material_head'] = 'Ajouter un nouveau BOM';
$lang['manufacturing_item_master_head'] = 'MASTER ARTICLE';
$lang['manufacturing_main'] = 'Principal';
$lang['manufacturing_category'] = 'Cat...';
$lang['manufacturing_category_title'] = 'Catégorie';
$lang['manufacturing_current_stock_title'] = 'Stock actuel';
$lang['manufacturing_link_item'] = 'Lier l&apos;article';
$lang['manufacturing_overhead_head'] = 'FRAIS GÉNÉRAUX';
$lang['manufacturing_machine'] = 'MACHINE MFQ';
$lang['manufacturing_link_to_erp'] = 'Lier à l&apos;ERP';
$lang['manufacturing_manage_machine'] = 'GÉRER LA MACHINE';
$lang['manufacturing_machine_information'] = 'Informations sur la machine';
$lang['manufacturing_comments'] = 'Commentaires';
$lang['manufacturing_pull_item_from_erp'] = 'Extraire l&apos;article de l&apos;ERP';
$lang['manufacturing_job_order'] = 'Ordre de travail';
$lang['manufacturing_item_from_erp'] = 'Article de l&apos;ERP';
$lang['manufacturing_asset_from_erp'] = 'Actif de l&apos;ERP';
$lang['manufacturing_add_asset'] = 'Ajouter un actif';
$lang['manufacturing_machine_name'] = 'Nom de la machine';
$lang['manufacturing_manufacture'] = 'Fabrication';
$lang['manufacturing_edit_machine'] = 'Modifier la machine';
$lang['manufacturing_manage_crew'] = 'Gérer l&apos;équipe';
$lang['manufacturing_manage_customer'] = 'Gérer le client';
$lang['manufacturing_customers_from_erp'] = 'Clients de l&apos;ERP';
$lang['manufacturing_segments_from_erp'] = 'Segments de l&apos;ERP';
$lang['manufacturing_add_workflow'] = 'Ajouter un workflow';
$lang['manufacturing_crew'] = 'Équipe';
$lang['manufacturing_progress'] = 'Progrès';
$lang['manufacturing_overhead'] = 'Frais généraux';
$lang['manufacturing_add_customer_inquiry'] = 'Ajouter une demande client';
$lang['manufacturing_add_estimate'] = 'Ajouter une estimation';
$lang['manufacturing_add_customer_invoice'] = 'Ajouter une facture client';
$lang['manufacturing_add_delivery_note'] = 'Ajouter un bon de livraison';

$lang['manufacturing_costing_configuration'] = 'Configuration des coûts';
$lang['manufacturing_usage_update'] = 'Mise à jour de l&apos;utilisation';
$lang['manufacturing_for_entries'] = 'Pour les entrées';
$lang['manufacturing_manual'] = 'Manuel';
$lang['manufacturing_linked_document'] = 'Document lié';

/** Document setup*/
$lang['manufacturing_document_setup'] = 'Configuration des documents';
$lang['manufacturing_add_document_setup'] = 'Ajouter une configuration de document';

$lang['manufacturing_awarded_job_status'] = 'Statut du travail attribué';
$lang['manufacturing_estimated_job_return'] = 'Retour estimé du travail';
$lang['manufacturing_document_configuration'] = 'Configuration du document';
$lang['manufacturing_gl_configuration'] = 'Configuration GL';
$lang['manufacturing_item_configuration'] = 'Configuration des articles';
$lang['manufacturing_policy_configuration'] = 'Configuration de la politique';
$lang['manufacturing_unbilled_jobs'] = 'Travaux non facturés';

$lang['manufacturing_estimate_proposal_approval'] = 'Approbation de la proposition d&apos;estimation';

$lang['manufacturing_Finance'] = 'Finance';

$lang['manufacturing_stages'] = 'Étapes';
$lang['manufacturing_add_stages'] = 'Ajouter des étapes';
$lang['manufacturing_edit_stages'] = 'Modifier les étapes';

$lang['manufacturing_edit_stages'] = 'Modifier les étapes';
$lang['manufacturing_weightage'] = 'Poids';

$lang['manufacturing_packaging'] = 'Emballage';
$lang['manufacturing_add_bom_material_consumption_head'] = 'Consommation de matériaux';

$lang['manufacturing_detail'] = 'Détail';
$lang['manufacturing_estimated_employee'] = 'Employé estimé';
$lang['manufacturing_sales_manager'] = 'Responsable des ventes';
$lang['manufacturing_sales_marketing'] = 'Ventes & Marketing';
$lang['manufacturing_recipt_warehouse'] = 'Réception à l&apos;entrepôt.';
$lang['manufacturing_add_recipt_warehouse'] = 'Ajouter une réception à l&apos;entrepôt.';
$lang['manufacturing_quotation'] = 'Devis';
