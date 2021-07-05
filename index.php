<?php
    #   Autoload classes
    function __autoload($class)
    {require_once $class.".class.php";}
    include_once "cep.php";

    #   Objeto
    $obj  = new Distancia();
    $list = $obj->GetData();

    $act     = "add";
    $cd      = "";
    $origem  = "";
    $destino = "";
    if (isset($_GET["act"]) && $_GET["act"] == "get") {
        $list2 = $obj->GetData($_GET["cd"]);
        $list2 = $list2[0];
        $act     = "edit";
        $cd      = $list2->get("cd");
        $origem  = $list2->get("origem");
        $destino = $list2->get("destino");
    }
?>


<form name="form1" method="get" action="index.php">
  Coloque os CEP's saber a dist&acirc;ncia<br>
  <br>
  Origem
  <input name="origem" type="text" value="<?php echo $origem; ?>" size="50">
  Destino
  <input name="destino" type="text" value="<?php echo $destino; ?>" size="50">

  <input name="act" type="hidden" value="<?php echo $act; ?>" size="25">
  <input name="cd" type="hidden" value="<?php echo $cd; ?>" size="25">
  <input type="submit" value="Calcular">
</form>


<?php
include "dist_list.php";
?>