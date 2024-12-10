<?php
/**
 * System messages translation for CodeIgniter(tm)
 *
 * @prefix : navigation_menu_
 * Created on 28-June-2017
 */
$languageflowserve = getPolicyValues('LNG', 'All');
$isGCC = getPolicyValues('MANFL', 'All');

$lang['navigation_menu__dashboard'] = 'Tableau de bord';
$lang['navigation_menu__procurement'] = 'Achats';
$lang['navigation_menu__inventory'] = 'Inventaire';
$lang['navigation_menu__accounts_payable'] = 'Comptes fournisseurs';
$lang['navigation_menu__accounts_receivable'] = 'Comptes clients';
$lang['navigation_menu__finance'] = 'Comptabilité';
$lang['navigation_menu__asset_management'] = 'Gestion des immobilisations';
$lang['navigation_menu__treasury'] = 'Trésorerie';
$lang['navigation_menu__hrms'] = 'RHMS';
$lang['navigation_menu__configuration'] = 'Paramètres';
$lang['navigation_menu__tax'] = 'Impôt';
$lang['navigation_menu__pos_restaurant'] = 'POS Restaurant';
$lang['navigation_menu__administration'] = 'Gestion';
$lang['navigation_menu__pos_general'] = 'POS Général';
$lang['navigation_menu__my_profile'] = 'Service personnel';

////special company request for change navigation menu Manufacturing to Operation/Service using FlowServe policy
if($languageflowserve=='FlowServe'){
    $lang['navigation_menu__manufacturing'] = 'Opération/Service'; 
}else{
    $lang['navigation_menu__manufacturing'] = 'Fabrication';
}

if (in_array($languageflowserve, ['MSE', 'SOP', 'GCC','Nov', 'Flowserve','Micoda'])) {
    $lang['navigation_menu_38_expense_claim'] = 'Réclamation des employés';
    $lang['navigation_menu_329_expense_claim'] = 'Réclamation des employés';
    $lang['navigation_menu_152_segment'] = 'Centre de coûts';
} else {
    $lang['navigation_menu_38_expense_claim'] = 'Réclamation de dépenses';
    $lang['navigation_menu_329_expense_claim'] = 'Réclamation de dépenses';
    $lang['navigation_menu_152_segment'] = 'Segment';
}

$lang['navigation_menu__sales___marketing'] = 'Ventes';
$lang['navigation_menu__crm'] = 'CRM';
$lang['navigation_menu__mpr'] = 'MPR';
$lang['navigation_menu__dashboard'] = 'Analyse AI';
$lang['navigation_menu__qhse'] = 'QHSE';
$lang['navigation_menu__vdr'] = 'VDR';
$lang['navigation_menu__fleet_management'] = 'Gestion de la flotte';
$lang['navigation_menu__srm'] = 'SRM';
$lang['navigation_menu__group_management'] = 'Gestion de groupe';
$lang['navigation_menu__project_management'] = 'Gestion de projet';
$lang['navigation_menu__buyback'] = 'Rachat';
$lang['navigation_menu__operation_ngo'] = 'Opération ONG';
$lang['navigation_menu__operation_ngo'] = 'Opération ONG';
$lang['navigation_menu__fund_management'] = 'Gestion des fonds';

$lang['navigation_menu_31_approval'] = 'Approbation';
$lang['navigation_menu_31_transactions'] = 'Transactions';
$lang['navigation_menu_31_report'] = 'Rapport';
$lang['navigation_menu_31_masters'] = 'Maîtres';
$lang['navigation_menu_32_approval'] = 'Approbation';
$lang['navigation_menu_32_transactions'] = 'Transactions';
$lang['navigation_menu_32_report'] = 'Rapport';
$lang['navigation_menu_32_masters'] = 'Maîtres';
$lang['navigation_menu_33_approval'] = 'Approbation';
$lang['navigation_menu_33_transactions'] = 'Transactions';
$lang['navigation_menu_33_report'] = 'Rapport';
$lang['navigation_menu_33_masters'] = 'Maîtres';
$lang['navigation_menu_34_approval'] = 'Approbation';
$lang['navigation_menu_34_transactions'] = 'Transactions';
$lang['navigation_menu_34_report'] = 'Rapport';
$lang['navigation_menu_35_approval'] = 'Approbation';
$lang['navigation_menu_35_transactions'] = 'Transactions';
$lang['navigation_menu_35_report'] = 'Rapport';
$lang['navigation_menu_35_masters'] = 'Maîtres';
$lang['navigation_menu_36_approval'] = 'Approbation';
$lang['navigation_menu_36_transactions'] = 'Transactions';
$lang['navigation_menu_36_report'] = 'Rapport';
$lang['navigation_menu_36_masters'] = 'Maîtres';
$lang['navigation_menu_37_approval'] = 'Approbation';
$lang['navigation_menu_37_transactions'] = 'Transactions';
$lang['navigation_menu_37_report'] = 'Rapport';
$lang['navigation_menu_38_leave_management'] = 'Gestion des congés';
$lang['navigation_menu_38_payroll'] = 'Paie';
$lang['navigation_menu_38_attendance'] = 'Présence';
$lang['navigation_menu_38_employee'] = 'Employé';
$lang['navigation_menu_38_other_masters'] = 'Autres Maîtres';

$lang['navigation_menu_38_approval'] = 'Approbation';
$lang['navigation_menu_38_loan'] = 'Prêt';
$lang['navigation_menu_38_report'] = 'Rapport';
$lang['navigation_menu_38_over_time_management'] = 'Gestion des heures supplémentaires';
$lang['navigation_menu_38_final_settlement'] = 'Règlement final';
$lang['navigation_menu_39_company_user_group'] = 'Groupe d&apos;utilisateurs de l&apos;entreprise';
$lang['navigation_menu_39_company_configuration'] = 'Configuration de l&apos;entreprise';
$lang['navigation_menu_39_user_configuration'] = 'Configuration de l&apos;utilisateur';
$lang['navigation_menu_39_template_configuration'] = 'Configuration des modèles';
$lang['navigation_menu_39_approval_setup'] = 'Configuration de l&apos;approbation';
$lang['navigation_menu_39_navigation_group_setup'] = 'Configuration du groupe de navigation';
$lang['navigation_menu_39_employee_navigation_access'] = 'Accès à la navigation des employés';
$lang['navigation_menu_39_document_setup'] = 'Configuration des documents';
$lang['navigation_menu_39_report_template'] = 'Modèle de rapport';
$lang['navigation_menu_40_masters'] = 'Maîtres';
$lang['navigation_menu_41_kitchen'] = 'Cuisine';
$lang['navigation_menu_41_pos_tablet'] = 'Tablette POS';
$lang['navigation_menu_41_dashboard'] = 'Tableau de bord';
$lang['navigation_menu_41_pos_terminal'] = 'Terminal POS';
$lang['navigation_menu_41_config'] = 'Configuration';
$lang['navigation_menu_41_masters'] = 'Maîtres';
$lang['navigation_menu_41_till_management_report'] = 'Rapport de gestion de caisse';
$lang['navigation_menu_41_outlet_sales_report'] = 'Rapport de ventes du point de vente';
$lang['navigation_menu_41_delivery_order'] = 'Commande de livraison';
$lang['navigation_menu_41_sales_report'] = 'Rapport de ventes';
$lang['navigation_menu_41_sales_detail_report'] = 'Rapport détaillé des ventes';
$lang['navigation_menu_41_outlet_item_wise_sales'] = 'Ventes par article du point de vente';
$lang['navigation_menu_41_pos_sales_report'] = 'Rapport de ventes POS';
$lang['navigation_menu_41_item_wise_sales'] = 'Ventes par article';
$lang['navigation_menu_41_product_mix'] = 'Mélange de produits';
$lang['navigation_menu_41_franchise'] = 'Franchise';
$lang['navigation_menu_41_delivery_commision_report'] = 'Rapport de commission de livraison';
$lang['navigation_menu_41_discount_report'] = 'Rapport de remise';
$lang['navigation_menu_41_reports'] = 'Rapports';
$lang['navigation_menu_42_reversing_approved_document'] = 'Annulation du document approuvé';
$lang['navigation_menu_42_company_policy'] = 'Politique de l&apos;entreprise';
$lang['navigation_menu_133_purchase_order'] = 'Bon de commande';
$lang['navigation_menu_133_purchase_request'] = 'Demande d&apos;achat';
$lang['navigation_menu_134_purchase_order'] = 'Bon de commande';
$lang['navigation_menu_134_purchase_request'] = 'Demande d&apos;achat';
$lang['navigation_menu_135_purchase_order_list'] = 'Liste des bons de commande';
$lang['navigation_menu_136_purchasing_address'] = 'Adresse d&apos;achat';

////special company request for change navigation menu  using Manufacturing Flow policy
$manufacturing_Flow = getPolicyValues('MANFL', 'All');
if($manufacturing_Flow == 'Micoda'){
    $lang['navigation_menu_137_goods_received_voucher'] = 'GRN';  
}else{
    $lang['navigation_menu_137_goods_received_voucher'] = 'Bon de réception de marchandises';   
}
$lang['navigation_menu_137_material_issue'] = 'Problème de matériel';
$lang['navigation_menu_137_stock_transfer'] = 'Transfert de stock';
$lang['navigation_menu_137_purchase_return'] = 'Retour d&apos;achat';
$lang['navigation_menu_137_stock_adjustment'] = 'Ajustement de stock';
$lang['navigation_menu_137_material_receipt_note'] = 'Note de réception de matériel';
$lang['navigation_menu_137_material_request'] = 'Demande de matériel';
$lang['navigation_menu_137_stock_counting'] = 'Comptage des stocks';
////special company request for change navigation menu  using Manufacturing Flow policy
if($manufacturing_Flow == 'Micoda'){
    $lang['navigation_menu_138_goods_received_voucher'] = 'GRN';
}else{
    $lang['navigation_menu_138_goods_received_voucher'] = 'Bon de réception de marchandises';
}
$lang['navigation_menu_138_purchase_return'] = 'Retour d&apos;achat';
$lang['navigation_menu_138_material_issue'] = 'Problème de matériel';
$lang['navigation_menu_138_stock_transfer'] = 'Transfert de stock';
$lang['navigation_menu_138_stock_adjustment'] = 'Ajustement de stock';
$lang['navigation_menu_138_material_receipt_note'] = 'Note de réception de matériel';
$lang['navigation_menu_138_material_request'] = 'Demande de matériel';
$lang['navigation_menu_138_stock_counting'] = 'Comptage des stocks';
$lang['navigation_menu_139_item_inquiry'] = 'Demande d&apos;article';
$lang['navigation_menu_139_item_ledger'] = 'Grand livre des articles';
$lang['navigation_menu_139_item_valuation_summary'] = 'Résumé de l&apos;évaluation des articles';
$lang['navigation_menu_139_item_counting'] = 'Comptage des articles';
$lang['navigation_menu_139_fast_moving_item'] = 'Article à rotation rapide';
$lang['navigation_menu_139_unbilled_grv'] = 'GRV non facturé';
$lang['navigation_menu_140_units_of_measurement'] = 'Unités de mesure';
$lang['navigation_menu_140_item_master'] = 'Maître des articles';
$lang['navigation_menu_140_item_category'] = 'Catégorie d&apos;article';
$lang['navigation_menu_140_grv_addon_category'] = 'Catégorie d&apos;addon GRV';
$lang['navigation_menu_140_warehouse_master'] = 'Maître des entrepôts';
$lang['navigation_menu_141_supplier_invoice'] = 'Facture fournisseur';
$lang['navigation_menu_141_debit_note'] = 'Note de débit';
$lang['navigation_menu_141_payment_voucher'] = 'Bon de paiement';
$lang['navigation_menu_142_supplier_invoice'] = 'Facture fournisseur';
$lang['navigation_menu_142_debit_note'] = 'Note de débit';
$lang['navigation_menu_142_payment_voucher'] = 'Bon de paiement';
$lang['navigation_menu_142_payment_matching'] = 'Appariement de paiement';
$lang['navigation_menu_142_payment_reversal'] = 'Annulation de paiement';
$lang['navigation_menu_143_vendor_ledger'] = 'Grand livre des fournisseurs';
$lang['navigation_menu_143_vendor_statement'] = 'Relevé des fournisseurs';
$lang['navigation_menu_143_vendor_aging_summary'] = 'Résumé de l&apos;ancienneté des fournisseurs';
$lang['navigation_menu_143_vendor_aging_detail'] = 'Détail de l&apos;ancienneté des fournisseurs';
$lang['navigation_menu_144_supplier_category'] = 'Catégorie de fournisseur';
$lang['navigation_menu_144_supplier_master'] = 'Maître des fournisseurs';
$lang['navigation_menu_145_credit_note'] = 'Note de crédit';
$lang['navigation_menu_145_receipt_voucher'] = 'Bon de réception';
$lang['navigation_menu_146_receipt_matching'] = 'Appariement des réceptions';
$lang['navigation_menu_146_credit_note'] = 'Note de crédit';
$lang['navigation_menu_146_receipt_voucher'] = 'Bon de réception';
$lang['navigation_menu_146_receipt_reversal'] = 'Annulation de réception';
$lang['navigation_menu_147_customer_ledger'] = 'Grand livre des clients';
$lang['navigation_menu_147_customer_statement'] = 'Relevé des clients';
$lang['navigation_menu_147_customer_aging_summary'] = 'Résumé de l&apos;ancienneté des clients';
$lang['navigation_menu_147_customer_aging_detail'] = 'Détail de l&apos;ancienneté des clients';
$lang['navigation_menu_147_collection_summary'] = 'Résumé des collections';
$lang['navigation_menu_147_collection_details'] = 'Détails des collections';
$lang['navigation_menu_149_journal_voucher'] = 'Bon de journal';
$lang['navigation_menu_149_recurring_jv'] = 'JV récurrente';
$lang['navigation_menu_150_journal_voucher'] = 'Bon de journal';
$lang['navigation_menu_150_budget'] = 'Budget';
$lang['navigation_menu_150_recurring_jv'] = 'JV récurrente';
$lang['navigation_menu_151_trial_balance'] = 'Balance de vérification';
$lang['navigation_menu_151_income_statement'] = 'Compte de résultat';
$lang['navigation_menu_151_balance_sheet'] = 'Bilan';
$lang['navigation_menu_151_general_ledger'] = 'Grand livre général';
$lang['navigation_menu_152_segment'] = 'Segment';
$lang['navigation_menu_152_financial_year'] = 'Année financière';
$lang['navigation_menu_152_chart_of_accounts'] = 'Plan comptable';
$lang['navigation_menu_153_asset'] = 'Actif';
$lang['navigation_menu_153_depreciation'] = 'Amortissement';
$lang['navigation_menu_153_disposal'] = 'Cession';
$lang['navigation_menu_154_asset_depreciation'] = 'Amortissement des actifs';
$lang['navigation_menu_154_asset_disposal'] = 'Cession d&apos;actifs';
$lang['navigation_menu_155_monthly_depreciation_report'] = 'Rapport mensuel d&apos;amortissement';
$lang['navigation_menu_155_asset_register'] = 'Registre des actifs';
$lang['navigation_menu_155_asset_register_summary'] = 'Résumé du registre des actifs';
$lang['navigation_menu_156_asset_master'] = 'Maître des actifs';
$lang['navigation_menu_156_asset_location'] = 'Emplacement des actifs';
$lang['navigation_menu_157_bank_reconciliation'] = 'Rapprochement bancaire';
$lang['navigation_menu_157_bank_transfer'] = 'Transfert bancaire';
$lang['navigation_menu_158_loan_management'] = 'Gestion des prêts';
$lang['navigation_menu_158_bank_reconciliation'] = 'Rapprochement bancaire';
$lang['navigation_menu_158_bank_transfer'] = 'Transfert bancaire';
$lang['navigation_menu_158_currency_exchange'] = 'Échange de devises';
$lang['navigation_menu_159_post_dated_cheque'] = 'Chèque postdaté';
$lang['navigation_menu_159_bank___cash_register'] = 'Registre bancaire / de caisse';
$lang['navigation_menu_161_salary_declaration'] = 'Déclaration salariale';
$lang['navigation_menu_161_machine_attendance'] = 'Présence machine';
$lang['navigation_menu_161_non_salary_processing'] = 'Traitement non salarial';
$lang['navigation_menu_161_fixed_element_declaration'] = 'Déclaration des éléments fixes';
$lang['navigation_menu_161_payroll_processing'] = 'Traitement de la paie';
$lang['navigation_menu_161_loan'] = 'Prêt';
$lang['navigation_menu_161_leave'] = 'Congé';
$lang['navigation_menu_161_attendance_summary'] = 'Résumé de la présence';
$lang['navigation_menu_161_final_settlement_approval'] = 'Approbation du règlement final';
$lang['navigation_menu_161_variable_pay_approval'] = 'Approbation de la rémunération variable';
$lang['navigation_menu_161_salary_advance_request_approval'] = 'Approbation de la demande d&apos;avance sur salaire';
$lang['navigation_menu_161_leave_encashment___salary_approval'] = 'Encaissement des congés / Approbation de salaire';
$lang['navigation_menu_162_employee_loan'] = 'Prêt salarié';
$lang['navigation_menu_162_loan_category'] = 'Catégorie de prêt';
$lang['navigation_menu_163_employee_pay_scale'] = 'Échelle salariale des employés';
$lang['navigation_menu_163_pay_slip'] = 'Fiche de paie';
$lang['navigation_menu_163_report_master'] = 'Maître des rapports';
$lang['navigation_menu_163_employee_leave_balance'] = 'Solde des congés des employés';
$lang['navigation_menu_163_etf'] = 'ETF';
$lang['navigation_menu_163_epf'] = 'EPF';
$lang['navigation_menu_163_allowance_slip'] = 'Fiche d&apos;allocation';
$lang['navigation_menu_163_c_form'] = 'Formulaire C';
$lang['navigation_menu_163_r_form'] = 'Formulaire R';
$lang['navigation_menu_163_etf_return'] = 'Retour ETF';
$lang['navigation_menu_163_payee_registration'] = 'Enregistrement PAYE';
$lang['navigation_menu_163_income_tax_deduction'] = 'Déduction de l&apos;impôt sur le revenu';
$lang['navigation_menu_163_salary_comparison'] = 'Comparaison salariale';
$lang['navigation_menu_163_localization'] = 'Localisation';
$lang['navigation_menu_163_salary_trend'] = 'Tendance salariale';
$lang['navigation_menu_163_employee_details_report'] = 'Rapport sur les détails des employés';
$lang['navigation_menu_163_employee_leave_history'] = 'Historique des congés des employés';
$lang['navigation_menu_169_tax'] = 'Impôt';
$lang['navigation_menu_169_tax_group'] = 'Groupe d&apos;impôts';
$lang['navigation_menu_169_tax_authority'] = 'Autorité fiscale';
$lang['navigation_menu_173_menu_master'] = 'Maître du menu';
$lang['navigation_menu_173_outlet_setup'] = 'Configuration des points de vente';
$lang['navigation_menu_173_create_outlets'] = 'Créer des points de vente';
$lang['navigation_menu_173_counter_setup'] = 'Configuration du comptoir';
$lang['navigation_menu_173_menu_size'] = 'Taille du menu';
$lang['navigation_menu_173_yield_setup'] = 'Configuration du rendement';
$lang['navigation_menu_173_customer___order_setup'] = 'Configuration Client / Commande';
$lang['navigation_menu_173_promotion_discount_setup'] = 'Configuration Promotion/Remise';
$lang['navigation_menu_173_yield_preparation'] = 'Préparation du Rendement';
$lang['navigation_menu_173_user_group'] = 'Groupe d&apos;Utilisateurs';
$lang['navigation_menu_173_authentication_process'] = 'Processus d&apos;Authentification';

$lang['navigation_menu_175_gl_configuration'] = 'Configuration du GL';
$lang['navigation_menu_175_counter'] = 'Comptoir';
$lang['navigation_menu_175_warehouse_users'] = 'Utilisateurs d&apos;entrepôt';
$lang['navigation_menu_175_crew_roles'] = 'Rôles de l&apos;équipe';
$lang['navigation_menu_175_outlet_users'] = 'Utilisateurs du point de vente';
$lang['navigation_menu_175_customers'] = 'Clients';
$lang['navigation_menu_175_card_master'] = 'Maître des cartes';


$lang['navigation_menu_285_template'] = 'Modèle';
$lang['navigation_menu_313_apply_for_leave'] = 'Demander un congé';
$lang['navigation_menu_313_leave_master'] = 'Maître des congés';
$lang['navigation_menu_313_leave_group'] = 'Groupe de congés';
$lang['navigation_menu_313_leave_calendar'] = 'Calendrier des congés';
$lang['navigation_menu_313_leave_adjustment'] = 'Ajustement des congés';
$lang['navigation_menu_313_monthly_leave_accrual'] = 'Accumulation mensuelle des congés';
$lang['navigation_menu_313_annual_leave_accrual'] = 'Accumulation annuelle des congés';
$lang['navigation_menu_313_leave_plan'] = 'Plan de congé';
$lang['navigation_menu_313_approval_setup'] = 'Configuration de l&apos;approbation';
$lang['navigation_menu_313_sick_leave_setup'] = 'Configuration du congé maladie';
$lang['navigation_menu_318_terminal'] = 'Terminal';
$lang['navigation_menu_318_masters'] = 'Maîtres';
$lang['navigation_menu_318_dashboard'] = 'Tableau de bord';
$lang['navigation_menu_324_gl_configuration'] = 'Configuration du GL';
$lang['navigation_menu_324_counter'] = 'Comptoir';
$lang['navigation_menu_324_warehouse_users'] = 'Utilisateurs d&apos;entrepôt';
$lang['navigation_menu_329_profile'] = 'Profil';
$lang['navigation_menu_329_pay_slip'] = 'Fiche de paie';
$lang['navigation_menu_329_apply_for_leave'] = 'Demander un congé';
$lang['navigation_menu_329_monthly_allowance_slip'] = 'Fiche d&apos;allocation mensuelle';

$lang['navigation_menu_329_approval'] = 'Approbation';
$lang['navigation_menu_329_leave_plan'] = 'Plan de congé';
$lang['navigation_menu_329_sales_target'] = 'Objectif de vente';
$lang['navigation_menu_329_iou_expenses'] = 'Dépenses IOU';
$lang['navigation_menu_329_salary_advance_request'] = 'Demande d&apos;avance sur salaire';
$lang['navigation_menu_329_attendance'] = 'Présence';
$lang['navigation_menu_329_purchase_request'] = 'Demande d&apos;achat';

$lang['navigation_menu_342_monthly_addition'] = 'Ajout Mensuel';
$lang['navigation_menu_342_monthly_deduction'] = 'Déduction Mensuelle';
$lang['navigation_menu_342_payroll_processing'] = 'Traitement de la Paie';
$lang['navigation_menu_342_monthly_add___ded'] = 'Ajout / Déduction Mensuelle';
$lang['navigation_menu_342_salary_category'] = 'Catégorie de Salaire';
$lang['navigation_menu_342_paysheet_template'] = 'Modèle de Fiche de Paie';
$lang['navigation_menu_342_salary_declaration'] = 'Déclaration de Salaire';
$lang['navigation_menu_342_social_insurance_master'] = 'Maître de l&apos;Assurance Sociale';
$lang['navigation_menu_342_slab_master'] = 'Maître des Tranches';
$lang['navigation_menu_342_paysheet_grouping'] = 'Regroupement des Fiches de Paie';
$lang['navigation_menu_342_payee_master'] = 'Maître des Payeurs';
$lang['navigation_menu_342_non_salary_processing'] = 'Traitement Non-Salaire';
$lang['navigation_menu_342_sso_slab_master'] = 'Maître des Tranches SSO';
$lang['navigation_menu_343_attendance_types'] = 'Types de Présence';
$lang['navigation_menu_343_shift_master'] = 'Maître des Postes';
$lang['navigation_menu_343_floor_master'] = 'Maître des Étages';
$lang['navigation_menu_343_over_time_master'] = 'Maître des Heures Supplémentaires';
$lang['navigation_menu_343_over_time_group_master'] = 'Maître des Groupes d&apos;Heures Supplémentaires';
$lang['navigation_menu_343_manual_attendance'] = 'Présence Manuelle';
$lang['navigation_menu_343_machine_attendance'] = 'Présence Machine';
$lang['navigation_menu_343_no_pay_setup'] = 'Configuration Sans Paie';
$lang['navigation_menu_343_machine_mapping'] = 'Cartographie des Machines';
$lang['navigation_menu_343_attendance_summary'] = 'Résumé de la Présence';
$lang['navigation_menu_343_attendance_template'] = 'Modèle de Présence';
$lang['navigation_menu_344_employee_master'] = 'Maître des Employés';
$lang['navigation_menu_344_bank_master'] = 'Maître des Banques';
$lang['navigation_menu_344_employee_type'] = 'Type d&apos;Employé';
$lang['navigation_menu_344_employee_non_payroll_bank'] = 'Banque Non-Paie des Employés';
$lang['navigation_menu_344_grade'] = 'Grade';
$lang['navigation_menu_345_department_master'] = 'Maître des Départements';
$lang['navigation_menu_345_document_setup'] = 'Configuration des Documents';
$lang['navigation_menu_345_religion_master'] = 'Maître des Religions';
$lang['navigation_menu_345_country_master'] = 'Maître des Pays';
$lang['navigation_menu_345_designation_master'] = 'Maître des Désignations';
$lang['navigation_menu_345_document_master'] = 'Maître des Documents';
$lang['navigation_menu_345_nationality_master'] = 'Maître des Nationalités';
$lang['navigation_menu_345_insurance_category'] = 'Catégorie d&apos;Assurance';
$lang['navigation_menu_345_hr_documents'] = 'Documents RH';
$lang['navigation_menu_348_job_cart'] = 'Panier de Travail';
$lang['navigation_menu_348_configuration'] = 'Configuration';
$lang['navigation_menu_348_masters'] = 'Maîtres';
$lang['navigation_menu_348_dashboard'] = 'Tableau de Bord';
$lang['navigation_menu_348_job'] = 'Emploi';
$lang['navigation_menu_348_customer_inquiry'] = 'Demande Client';

if($isGCC=='GCC'){
    $lang['navigation_menu_348_estimate'] = 'Devis';
}
else{
    $lang['navigation_menu_348_estimate'] = 'Estimation';
}

$lang['navigation_menu_348_approval'] = 'Approbation';

$lang['navigation_menu_350_bill_of_material'] = 'Liste des Matériaux';
$lang['navigation_menu_350_item_master'] = 'Maître des Articles';
$lang['navigation_menu_350_over_heads'] = 'Frais Généraux';
$lang['navigation_menu_350_asset_master'] = 'Maître des Actifs';
$lang['navigation_menu_350_crew'] = 'Équipe';
$lang['navigation_menu_350_company_workflow'] = 'Flux de Travail de l&apos;Entreprise';
$lang['navigation_menu_350_template'] = 'Modèle';
$lang['navigation_menu_350_machine'] = 'Machine';
$lang['navigation_menu_350_template_setup'] = 'Configuration du Modèle';
$lang['navigation_menu_350_customers'] = 'Clients';
$lang['navigation_menu_350_segment'] = 'Segment';
$lang['navigation_menu_350_workflow_setup'] = 'Configuration du Flux de Travail';
$lang['navigation_menu_350_unit_of_measure'] = 'Unité de Mesure';
$lang['navigation_menu_350_system_settings'] = 'Paramètres du Système';

$lang['navigation_menu_361_masters'] = 'Maîtres';
$lang['navigation_menu_361_transactions'] = 'Transactions';
$lang['navigation_menu_361_approval'] = 'Approbation';
$lang['navigation_menu_361_reports'] = 'Rapports';
$lang['navigation_menu_363_customer_category'] = 'Catégorie de Client';
$lang['navigation_menu_363_sales_person'] = 'Vendeur';
$lang['navigation_menu_363_customer_master'] = 'Maître des Clients';
$lang['navigation_menu_364_quotation___contract'] = 'Devis / Contrat';
$lang['navigation_menu_364_sales_commission'] = 'Commission de Vente';
$lang['navigation_menu_364_commission_payment'] = 'Paiement de Commission';
$lang['navigation_menu_364_sales_return'] = 'Retour de Vente';
$lang['navigation_menu_364_invoice'] = 'Facture';
$lang['navigation_menu_365_quotation___contract'] = 'Devis / Contrat';
$lang['navigation_menu_365_sales_commission'] = 'Commission de Vente';
$lang['navigation_menu_365_sales_return'] = 'Retour de Vente';
$lang['navigation_menu_365_commission_payment'] = 'Paiement de Commission';
$lang['navigation_menu_365_invoice'] = 'Facture';
$lang['navigation_menu_365_sales_order'] = 'Commande de Vente';
$lang['navigation_menu_365_revenue_details_report'] = 'Rapport de Détails de Revenus';
$lang['navigation_menu_365_revenue_details_summary'] = 'Résumé des Détails de Revenus';
$lang['navigation_menu_387_dashboard'] = 'Tableau de Bord';
$lang['navigation_menu_387_campaigns'] = 'Campagnes';
$lang['navigation_menu_387_tasks'] = 'Tâches';
$lang['navigation_menu_387_meetings'] = 'Réunions';
$lang['navigation_menu_387_contacts'] = 'Contacts';
$lang['navigation_menu_387_accounts'] = 'Comptes';
$lang['navigation_menu_387_leads'] = 'Prospects';
$lang['navigation_menu_387_opportunities'] = 'Opportunités';
$lang['navigation_menu_387_organizations'] = 'Organisations';
$lang['navigation_menu_387_system_settings'] = 'Paramètres du Système';
$lang['navigation_menu_387_reports'] = 'Rapports';
$lang['navigation_menu_387_projects'] = 'Projets';
$lang['navigation_menu_387_sales_target'] = 'Objectif de Vente';
$lang['navigation_menu_387_quotation'] = 'Devis';
$lang['navigation_menu_387_expense_claim'] = 'Demande de Remboursement de Dépenses';
$lang['navigation_menu_409_expense_claim_master'] = 'Maître des Demandes de Remboursement';
$lang['navigation_menu_409_expense_claim_category'] = 'Catégorie des Demandes de Remboursement';
$lang['navigation_menu_410_approval'] = 'Approbation';
$lang['navigation_menu_415_expense_claim'] = 'Demande de Remboursement de Dépenses';

$lang['navigation_menu_420_master'] = 'Maître';
$lang['navigation_menu_420_dashboard'] = 'Tableau de Bord';
$lang['navigation_menu_420_transactions'] = 'Transactions';
$lang['navigation_menu_421_srm_supplier_master'] = 'Maître des Fournisseurs SRM';
$lang['navigation_menu_421_srm_customer_master'] = 'Maître des Clients SRM';
$lang['navigation_menu_421_customer_order'] = 'Commande Client';
$lang['navigation_menu_441_configuration'] = 'Configuration';
$lang['navigation_menu_441_group_consolidation'] = 'Consolidation de Groupe';
$lang['navigation_menu_447_company_sub_groups'] = 'Sous-groupes d&apos;Entreprises';
$lang['navigation_menu_447_sub_group_employees'] = 'Employés du Sous-groupe';
$lang['navigation_menu_447_navigation_access'] = 'Accès de Navigation';
$lang['navigation_menu_447_sub_group_template_setup'] = 'Configuration du Modèle du Sous-groupe';
$lang['navigation_menu_448_customer_master'] = 'Maître des Clients';
$lang['navigation_menu_448_supplier_master'] = 'Maître des Fournisseurs';
$lang['navigation_menu_448_chart_of_accounts'] = 'Plan Comptable';
$lang['navigation_menu_448_customer_category'] = 'Catégorie de Client';
$lang['navigation_menu_448_supplier_category'] = 'Catégorie de Fournisseur';
$lang['navigation_menu_448_segment'] = 'Segment';
$lang['navigation_menu_448_item_category'] = 'Catégorie d&apos;Article';
$lang['navigation_menu_448_item_master'] = 'Maître des Articles';
$lang['navigation_menu_448_unit_of_measurement'] = 'Unité de Mesure';
$lang['navigation_menu_448_finance_year'] = 'Année Financière';
$lang['navigation_menu_448_warehouse'] = 'Entrepôt';

$lang['navigation_menu_454_item_category'] = 'Catégorie d&apos;Article';
$lang['navigation_menu_454_asset_category'] = 'Catégorie d&apos;Actif';
$lang['navigation_menu_458_fixed_elements'] = 'Éléments Fixes';
$lang['navigation_menu_399_fixed_elements'] = 'Éléments Fixes';
$lang['navigation_menu_458_over_time_group'] = 'Groupe d&apos;Heures Supplémentaires';
$lang['navigation_menu_399_over_time_group'] = 'Groupe d&apos;Heures Supplémentaires';
$lang['navigation_menu_458_over_time_slab'] = 'Tranche d&apos;Heures Supplémentaires';
$lang['navigation_menu_399_over_time_slab'] = 'Tranche d&apos;Heures Supplémentaires';
$lang['navigation_menu_458_monthly_addition'] = 'Ajout Mensuel';
$lang['navigation_menu_399_monthly_addition'] = 'Ajout Mensuel';
$lang['navigation_menu_458_fixed_element_declaration'] = 'Déclaration d&apos;Éléments Fixes';
$lang['navigation_menu_399_fixed_element_declaration'] = 'Déclaration d&apos;Éléments Fixes';
$lang['navigation_menu_466_project_master'] = 'Maître de Projet';
$lang['navigation_menu_466_project_category'] = 'Catégorie de Projet';
$lang['navigation_menu_466_project'] = 'Projet';
$lang['navigation_menu_466_project_planning'] = 'Planification de Projet';
$lang['navigation_menu_466_approval'] = 'Approbation';
$lang['navigation_menu_466_masters'] = 'Maîtres';
$lang['navigation_menu_481_project_approval'] = 'Approbation de Projet';
$lang['navigation_menu_483_project_category'] = 'Catégorie de Projet';
$lang['navigation_menu_483_project_master'] = 'Maître de Projet';

$lang['navigation_menu_478_order_inquiry'] = 'Enquête sur Commande';
$lang['navigation_menu_361_approval'] = 'Approbation';
$lang['navigation_menu_361_transactions'] = 'Transactions';
$lang['navigation_menu_361_masters'] = 'Maîtres';
$lang['navigation_menu_365_quotation___contract'] = 'Devis / Contrat';
$lang['navigation_menu_365_invoice'] = 'Facture';
$lang['navigation_menu_365_sales_commission'] = 'Commission de Vente';
$lang['navigation_menu_365_sales_return'] = 'Retour de Vente';
$lang['navigation_menu_365_commission_payment'] = 'Paiement de Commission';
$lang['navigation_menu_364_quotation___contract'] = 'Devis / Contrat';
$lang['navigation_menu_364_invoice'] = 'Facture';
$lang['navigation_menu_364_sales_commission'] = 'Commission de Vente';
$lang['navigation_menu_364_sales_return'] = 'Retour de Vente';
$lang['navigation_menu_364_commission_payment'] = 'Paiement de Commission';
$lang['navigation_menu_363_customer_master'] = 'Maître des Clients';
$lang['navigation_menu_363_customer_category'] = 'Catégorie de Client';
$lang['navigation_menu_363_sales_person'] = 'Vendeur';
$lang['navigation_menu_421_customer_order'] = 'Commande Client';
$lang['navigation_menu_478_order_inquiry'] = 'Enquête sur Commande';
$lang['navigation_menu_478_order_review'] = 'Révision de Commande';
$lang['navigation_menu_478_customer_order'] = 'Commande Client';

$lang['navigation_menu_501_approval'] = 'Approbation';
$lang['navigation_menu_501_reports'] = 'Rapports';
$lang['navigation_menu_501_masters'] = 'Maîtres';
$lang['navigation_menu_501_transaction'] = 'Transaction';

$lang['navigation_menu_537_dispatch_note'] = 'Note d&apos;Expédition';
$lang['navigation_menu_537_grn'] = 'GRN';
$lang['navigation_menu_537_payment_voucher'] = 'Bon de Paiement';
$lang['navigation_menu_537_batch_closing'] = 'Fermeture de Lot';

$lang['navigation_menu_502_dispatch_note'] = 'Note d&apos;Expédition';
$lang['navigation_menu_502_mortality'] = 'Mortalité';
$lang['navigation_menu_502_payment_voucher'] = 'Bon de Paiement';
$lang['navigation_menu_502_goods_received_note'] = 'Note de Réception de Marchandises';

$lang['navigation_menu_501_farm'] = 'Ferme';
$lang['navigation_menu_501_batch'] = 'Lot';
$lang['navigation_menu_501_add_on_category'] = 'Catégorie d&apos;Add-on';
$lang['navigation_menu_501_item_master'] = 'Maître des Articles';
$lang['navigation_menu_501_mortality_causes'] = 'Causes de Mortalités';
$lang['navigation_menu_501_production_report'] = 'Rapport de Production';


$lang['navigation_menu_519_approval'] = 'Approbation';
$lang['navigation_menu_519_donor_collection'] = 'Collecte de Donateurs';
$lang['navigation_menu_519_donor_commitments'] = 'Engagements des Donateurs';
$lang['navigation_menu_519_donor_collections'] = 'Collectes des Donateurs';
$lang['navigation_menu_519_transactions'] = 'Transactions';

$lang['navigation_menu_519_masters'] = 'Maîtres';
$lang['navigation_menu_519_donors'] = 'Donateurs';
$lang['navigation_menu_519_projects'] = 'Projets';
$lang['navigation_menu_519_beneficiary'] = 'Bénéficiaire';

$lang['navigation_menu_519_document_master'] = 'Maître des Documents';
$lang['navigation_menu_519_document_setup'] = 'Configuration des Documents';
$lang['navigation_menu_519_area_setup'] = 'Configuration des Zones';
$lang['navigation_menu_519_beneficiary_types'] = 'Types de Bénéficiaires';
$lang['navigation_menu_519_configuration'] = 'Configuration';
$lang['navigation_menu_161_final_settlement'] = 'Règlement Final';
$lang['navigation_menu_161_gl_configuration'] = 'Configuration GL';
$lang['navigation_menu_342_variable_pay_declaration'] = 'Déclaration de Rémunération Variable';
$lang['navigation_menu_342_gratuity_setup'] = 'Configuration de la Gratification';
$lang['navigation_menu_348_standard_job_card'] = 'Carte de Travail Standard';
$lang['navigation_menu_348_customer_invoice'] = 'Facture Client';

if($isGCC=='GCC'){
    $lang['navigation_menu_348_delivery_note'] = 'Réception au Magasin';
}
else{
    $lang['navigation_menu_348_delivery_note'] = 'Bon de Livraison';
}

$lang['navigation_menu_364_delivery_order'] = 'Bon de Livraison';
$lang['navigation_menu_365_delivery_order'] = 'Bon de Livraison';
$lang['navigation_menu_363_discount_and_extra_charges'] = 'Remise et Frais Supplémentaires';
$lang['navigation_menu_365_revenue_summary'] = 'Résumé des Revenus';
$lang['navigation_menu_365_sales_person_performance'] = 'Performance du Vendeur';
$lang['navigation_menu_365_unbilled_invoices'] = 'Factures Non Facturées';
$lang['navigation_menu_139_stock_aging'] = 'Vieillissement du Stock';
$lang['navigation_menu_139_itemwise_profitablity'] = 'Rentabilité par Article';
$lang['navigation_menu_140_item_attribute_assign'] = 'Attribution d&apos;Attributs d&apos;Article';
$lang['navigation_menu_143_vendor_balance'] = 'Solde Fournisseur';
$lang['navigation_menu_143_vendor_balance'] = 'Solde Fournisseur';
$lang['navigation_menu_147_customer_balance'] = 'Solde Client';
$lang['navigation_menu_149_budget'] = 'Budget';
$lang['navigation_menu_151_budget'] = 'Budget';
$lang['navigation_menu_158_cheque_register'] = 'Registre des Chèques';
$lang['navigation_menu_31_approval'] = 'Approbation';
$lang['navigation_menu_31_transaction'] = 'Transaction';
$lang['navigation_menu_32_masters'] = 'Masters';
$lang['navigation_menu_1109_iou_voucher'] = 'Bon IOU';
$lang['navigation_menu_1109_iou_booking'] = 'Réservation IOU';
$lang['navigation_menu_1109_iou_category'] = 'Catégorie IOU';
$lang['navigation_menu_1109_iou_user'] = 'Utilisateur IOU';
$lang['navigation_menu_169_tax_formula'] = 'Formule Fiscale';
$lang['navigation_menu_1065_tax_statement'] = 'Déclaration Fiscale';
$lang['navigation_menu_40_report'] = 'Rapport';
$lang['navigation_menu_344_employment_type'] = 'Type d&apos;Emploi';
$lang['navigation_menu_313_leave_encashment___salary'] = 'Encaissement de Congé / Approbation du Salaire';

$lang['navigation_menu_163_gratuity_salary'] = 'Salaire de Gratification';
$lang['navigation_menu_163_social_insurance'] = 'Assurance Sociale';
$lang['navigation_menu_163_employee_birth_day_report'] = 'Rapport d&apos;Anniversaire des Employés';
$lang['navigation_menu_163_employee_contract_expiry'] = 'Expiration du Contrat d&apos;Employé';
$lang['navigation_menu_163_employee_service_analysis'] = 'Analyse du Service des Employés';


$lang['navigation_menu_1109_approval'] = 'Approbation';
$lang['navigation_menu_1109_report'] = 'Rapport';
$lang['navigation_menu_1109_master'] = 'Maître';
$lang['navigation_menu_1109_transaction'] = 'Transaction';

$lang['navigation_menu_1110_fuel_usage'] = 'Utilisation du Carburant';
$lang['navigation_menu_1110_journey_plan'] = 'Plan de Voyage';

$lang['navigation_menu_1109_fuel_usage'] = 'Utilisation du Carburant';
$lang['navigation_menu_1109_vehicle_master'] = 'Maître du Véhicule';
$lang['navigation_menu_1109_driver_master'] = 'Maître du Conducteur';
$lang['navigation_menu_1109_fuel_types'] = 'Types de Carburant';
$lang['navigation_menu_1109_fuel_usage_report'] = 'Rapport d&apos;Utilisation du Carburant';
$lang['navigation_menu_1109_expense_category'] = 'Catégorie de Dépenses';
$lang['navigation_menu_1109_journey_plan'] = 'Plan de Voyage';
$lang['navigation_menu_1109_vehicle_maintenance'] = 'Entretien du Véhicule';
$lang['navigation_menu_1109_maintenance_criteria'] = 'Critères de Maintenance';
$lang['navigation_menu_519_operation'] = 'Opération';


$lang['navigation_menu_1107_company_master'] = 'Maître de l&apos;Entreprise';
$lang['navigation_menu_1107_investment_types'] = 'Types d&apos;Investissement';
$lang['navigation_menu_1107_investment'] = 'Investissement';
$lang['navigation_menu_1107_document_setup'] = 'Configuration des Documents';
$lang['navigation_menu_1107_financials'] = 'Finances';
$lang['navigation_menu_1107_report'] = 'Rapport';
$lang['navigation_menu_1153_income_statement'] = 'Compte de Résultat';
$lang['navigation_menu_42_reversing_approved_document'] = 'Annuler Document Approuvé';
$lang['navigation_menu_39_terms___conditions'] = 'Conditions Générales';
$lang['navigation_menu_39_payroll_access'] = 'Accès à la Paie';

$lang['navigation_menu_350_labour'] = 'Main-d&apos;œuvre';
$lang['navigation_menu_350_warehouse'] = 'Entrepôt';
$lang['navigation_menu_350_user_groups'] = 'Groupes d&apos;Utilisateurs';
$lang['navigation_menu_350_standard_details'] = 'Détails Standards';

$lang['navigation_menu_329_my_tasks'] = 'Mes Tâches';
$lang['navigation_menu_329_my_appraisal'] = 'Mon Évaluation';
$lang['navigation_menu_329_request_letters'] = 'Lettres de Demande';

$lang['navigation_menu_134_purchase_order_buy_back'] = 'Rachat de Bon de Commande';

$lang['navigation_menu_138_goods_received_voucher_2'] = 'Bon de Réception des Marchandises 2';
$lang['navigation_menu_365_quotation___contract_buy_back'] = 'Rachat de Devis / Contrat';
$lang['navigation_menu_365_sales_return_buy_back'] = 'Rachat de Retour de Vente';
$lang['navigation_menu_365_customer_price_setup'] = 'Configuration des Prix Client';

$lang['navigation_menu_364_quotation___contract_buy_back'] = 'Rachat de Devis / Contrat';
$lang['navigation_menu_364_sales_return_buy_back'] = 'Rachat de Retour de Vente';
$lang['navigation_menu_364_day_close'] = 'Clôture du Jour';

$lang['navigation_menu_363_customer_price_setup'] = 'Configuration des Prix Client';
$lang['navigation_menu_363_insurance_types'] = 'Types d&apos;Assurance';

$lang['navigation_menu_365_item_wise_sales_report'] = 'Rapport de Vente par Article';
$lang['navigation_menu_365_sales_analysis_report'] = 'Rapport d&apos;Analyse des Ventes';

$lang['navigation_menu_387_mail_box'] = 'Boîte aux Lettres';

$lang['navigation_menu_344_document_request'] = 'Demande de Document';

$lang['navigation_menu_163_grade_wise_salary_cost'] = 'Coût salarial par grade';
$lang['navigation_menu_163_attendance'] = 'Présence';
$lang['navigation_menu_163_leave_cost'] = 'Coût des congés';
$lang['navigation_menu_163_document_expiry'] = 'Expiration des documents';
$lang['navigation_menu_163_audit_report'] = 'Rapport d&apos;audit';
$lang['navigation_menu_163_etf'] = 'ETF';
$lang['navigation_menu_163_epf'] = 'EPF';

$lang['navigation_menu_345_commission_scheme'] = 'Plan de commission';
$lang['navigation_menu_345_travel_frequency'] = 'Fréquence de voyage';

$lang['navigation_menu_41_portable_pos'] = 'POS portable';
$lang['navigation_menu_41_kitchen_manual'] = 'Manuel de cuisine';

$lang['navigation_menu__performance_appraisal'] = 'Évaluation de la performance';
$lang['navigation_menu__operation'] = 'Opération';

$lang['navigation_menu_318_reports'] = 'Rapports';
$lang['navigation_menu_1099_sales_report'] = 'Rapport de ventes';
$lang['navigation_menu_1099_item_wise_sales_report'] = 'Rapport de ventes par article';
$lang['navigation_menu_1099_item_wise_profitability_report'] = 'Rapport de rentabilité par article';
$lang['navigation_menu_1099_sales_detail_report'] = 'Rapport détaillé des ventes';

$lang['navigation_menu_42_group_structure'] = 'Structure du groupe';
$lang['navigation_menu_42_group_structure_setup'] = 'Configuration de la structure du groupe';
$lang['navigation_menu_42_subscription'] = 'Abonnement';

$lang['navigation_menu_501_dashboard'] = 'Tableau de bord';
$lang['navigation_menu_501_configuration'] = 'Configuration';

$lang['navigation_menu_501_batch_creation'] = 'Création de lot';
$lang['navigation_menu_501_goods_received_note'] = 'Note de réception des marchandises';
$lang['navigation_menu_501_vouchers'] = 'Bons';
$lang['navigation_menu_501_return'] = 'Retour';

$lang['navigation_menu_502_live_collection'] = 'Collecte en direct';
$lang['navigation_menu_502_return'] = 'Retour';
$lang['navigation_menu_502_vouchers'] = 'Bons';
$lang['navigation_menu_502_farm_visit_report'] = 'Rapport de visite à la ferme';

$lang['navigation_menu_501_feed_types'] = 'Types d&apos;aliments';
$lang['navigation_menu_501_task_types'] = 'Types de tâches';
$lang['navigation_menu_501_farm_visit_task'] = 'Tâche de visite à la ferme';

$lang['navigation_menu_501_production_statement'] = 'État de la production';
$lang['navigation_menu_501_feed_schedule'] = 'Calendrier des aliments';
$lang['navigation_menu_501_batch_performance'] = 'Performance des lots';
$lang['navigation_menu_501_farm_ledger'] = 'Grand livre de la ferme';
$lang['navigation_menu_501_wip_report'] = 'Rapport WIP';
$lang['navigation_menu_501_outstanding'] = 'En suspens';
$lang['navigation_menu_501_batch_aging_report'] = 'Rapport de vieillissement des lots';
$lang['navigation_menu_501_monthly_summary'] = 'Résumé mensuel';
$lang['navigation_menu_501_batch_tracing'] = 'Traçabilité des lots';

$lang['navigation_menu_501_feed_chart'] = 'Graphique des aliments';
$lang['navigation_menu_501_area_setup'] = 'Configuration de la zone';
$lang['navigation_menu_501_policy'] = 'Politique';

$lang['navigation_menu_39_location'] = 'Emplacement';
$lang['navigation_menu_39_mpr_template_setup'] = 'Configuration du modèle MPR';

$lang['navigation_menu_135_purchase_order_tracking'] = 'Suivi des commandes d&apos;achat';
$lang['navigation_menu_140_percentage_setup'] = 'Configuration du pourcentage';
$lang['navigation_menu_34_masters'] = 'Masters';
$lang['navigation_menu_163_sponsor_wise_salary'] = 'Salaire par sponsor';
$lang['navigation_menu_163_salary_breakup_report'] = 'Rapport de répartition des salaires';

$lang['navigation_menu_138_exceeded_items'] = 'Articles dépassés';
$lang['navigation_menu_138_item_master_report'] = 'Rapport principal des articles';
$lang['navigation_menu_138_below_min_stock__rol'] = 'Stock inférieur au minimum / ROL';
$lang['navigation_menu_149_budget_transfer'] = 'Transfert de budget';
$lang['navigation_menu_150_budget_transfer'] = 'Transfert de budget';
$lang['navigation_menu_345_sponsor'] = 'Sponsor';
//$lang['navigation_menu_41_kitchen_countdown'] = 'Compte à rebours de la cuisine';
$lang['navigation_menu_1236_dashboard'] = 'Tableau de bord';
$lang['navigation_menu_32_activity'] = 'Activité';
$lang['navigation_menu_32_reports'] = 'Rapports';
$lang['navigation_menu_136_corporate_goal'] = 'Objectif d&apos;entreprise';
$lang['navigation_menu_136_employee_wise_performance'] = 'Performance par employé';
$lang['navigation_menu_136_soft_skills_based_performance'] = 'Performance basée sur les compétences comportementales';
$lang['navigation_menu_136_performance_evaluation_summary'] = 'Résumé de l&apos;évaluation de la performance';
$lang['navigation_menu_136_department'] = 'Département';
$lang['navigation_menu_136_corporate_objective'] = 'Objectif d&apos;entreprise';
$lang['navigation_menu_136_soft_skills'] = 'Compétences comportementales';
$lang['navigation_menu_136_approval_setup'] = 'Configuration de l&apos;approbation';
$lang['navigation_menu_136_department_appraisal'] = 'Évaluation du département';

/**duplicated */



