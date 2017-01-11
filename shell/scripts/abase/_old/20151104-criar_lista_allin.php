<?php
require_once './configScript.php';
/**
 * Created by PhpStorm.
 * User: julio
 * Date: 4/15/15
 * Time: 6:23 PM
 */
$lista = array(
	'nm_lista' => 'abase_integravet_2',
	'campos' => array(
		array("nome" => "nm_email", "tipo" => "texto", "tamanho" => "255", "unico" => "1"),
		array("nome" => "email_secundario", "tipo" => "texto", "tamanho" => "100"),
		array("nome" => "grupo_cliente", "tipo" => "texto", "tamanho" => "50"),
		array("nome" => "nome_fantasia", "tipo" => "texto", "tamanho" => "100"),
		array("nome" => "nome", "tipo" => "texto", "tamanho" => "50"),
		array("nome" => "sobrenome", "tipo" => "texto", "tamanho" => "50"),
		array("nome" => "telefone", "tipo" => "texto", "tamanho" => "30"),
		array("nome" => "cidade", "tipo" => "texto", "tamanho" => "30"),
		array("nome" => "estado", "tipo" => "texto", "tamanho" => "30"),
		array("nome" => "distribuidora", "tipo" => "texto", "tamanho" => "30"),
		array("nome" => "ja_fez_login", "tipo" => "texto", "tamanho" => "3"),
		array("nome" => "comprou_a_mais_de_45", "tipo" => "texto", "tamanho" => "3"),
		array("nome" => "comprou_a_menos_de_45", "tipo" => "texto", "tamanho" => "3"),
		array("nome" => "rep_nome1", "tipo" => "texto", "tamanho" => "50"),
		array("nome" => "rep_fone1", "tipo" => "texto", "tamanho" => "30"),
		array("nome" => "rep_foto1", "tipo" => "texto", "tamanho" => "150"),
		array("nome" => "rep_nome2", "tipo" => "texto", "tamanho" => "50"),
		array("nome" => "rep_fone2", "tipo" => "texto", "tamanho" => "30"),
		array("nome" => "rep_foto2", "tipo" => "texto", "tamanho" => "150"),
		array("nome" => "dt_alteracao", "tipo" => "data", "formato" => "dd-mm-aaaa"),
		array("nome" => "status_allin", "tipo" => "numero"),
		array("nome" => "status_email_secundario", "tipo" => "texto", "tamanho" => "1"),
		//=== marcas ===
		array("nome" => "bayer", "tipo" => "numero"),
		array("nome" => "biochek", "tipo" => "numero"),
		array("nome" => "bioctal", "tipo" => "numero"),
		array("nome" => "biooscientific", "tipo" => "numero"),
		array("nome" => "hill_s", "tipo" => "numero"),
		array("nome" => "ssi", "tipo" => "numero"),
		array("nome" => "zoetis", "tipo" => "numero")
	));

try
{
	$helper = Mage::helper('fvets_allin');
	$accounts = Mage::getModel('fvets_allin/account')
		->getCollection()
		->addFieldToFilter('website_id', $websiteId);

	foreach ($accounts as $account)
	{
		$customerControll = Mage::getModel('fvets_allin/customers');
		$customerControll->createListIfNotExists($account->getId(), $lista);
	}
} catch (Exception $ex)
{
	Mage::throwException($ex->__toString());
}