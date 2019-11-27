<?php
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); 
require("verifica.php");
require("conecta.php");

extract($_POST);

$_docxml = new DOMdocument('1.0','utf-8');
$_resp = $_docxml->createElement('itens');
$_att =  $_docxml->createAttribute("id");
$_id =    $_docxml->createTextNode($_SESSION["v_id"]);
$_att->appendChild($_id);
$_resp->appendChild($_att);

$codigo = utf8_decode($codigo);
$tipo = utf8_decode($tipo);
$protocolo = utf8_decode($protocolo);
$dtprotocolo = utf8_decode($dtprotocolo);
$datainicio = utf8_decode($datainicio);
$datafim = utf8_decode($datafim);
$operacao = utf8_decode($operacao);
$sit_datainicio = utf8_decode($sit_datainicio);
$sit_datafim = utf8_decode($sit_datafim);
$cid = utf8_decode(strtoupper($cid));
$base_legal = utf8_decode($base_legal);


$curs = ocinewcursor($conn);
$stmt = ociparse($conn,"begin rh.pck_juntamed.InsereAtestado(:InUsuario, :InCdServ, :InCdMotAfast, :InProtocolo, :InDtProtocolo, :InDataIni, :InDataFim, :InSituacao, :InDataIniOp, :InDataFimOp, :InCdCid, :InCdBaseLegal, :OutResultado); end;");
		ocibindbyname ($stmt,":InUsuario",$_SESSION["v_id"]);
		ocibindbyname ($stmt,":InCdServ",$codigo);
		ocibindbyname ($stmt,":InCdMotAfast",$tipo);
		ocibindbyname ($stmt,":InProtocolo",$protocolo);
		ocibindbyname ($stmt,":InDtProtocolo",$dtprotocolo);
		ocibindbyname ($stmt,":InDataIni",$datainicio);
		ocibindbyname ($stmt,":InDataFim",$datafim); 	
		ocibindbyname ($stmt,":InSituacao",$operacao);
		ocibindbyname ($stmt,":InDataIniOp",$sit_datainicio);
		ocibindbyname ($stmt,":InDataFimOp",$sit_datafim);
		ocibindbyname ($stmt,":InCdCid",$cid);
		ocibindbyname ($stmt,":InCdBaseLegal",$base_legal);
		ocibindbyname ($stmt,":OutResultado",$curs,-1,OCI_B_CURSOR);
		$r = @ociexecute($stmt);
		if (!$r) {
			erro ($stmt);
		}
		ociexecute($curs);

		while (ocifetchinto($curs,$outresultado)){
			//MONTA XML RETORNO
            $_item = $_docxml->createElement('item');

            $_cpo = $_docxml->createElement('msg4');
			$_des = $_docxml->createTextNode(utf8_encode($outresultado[0]));
            $_cpo->appendChild($_des);
            $_item->appendChild($_cpo);

    $_resp->appendChild($_item);

            }
        $_docxml->appendChild($_resp);

		OCIFreeStatement($stmt);
		OCIFreeCursor($curs);
		OCILogoff($conn);
Header("Content-type: application/xml; charset=utf-8");
echo $_docxml->saveXML();

?>
