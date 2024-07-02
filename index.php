<?php
//lucas 09102023 novo padrao
include_once __DIR__ . "/../config.php";
include_once "header.php";
include_once ROOT . "/sistema/database/loginAplicativo.php";

$nivelMenuLogin = buscaLoginAplicativo($_SESSION['idLogin'], 'Financeiro');

$configuracao = 1;
$nivelMenu = $nivelMenuLogin['nivelMenu'];

?>
<!doctype html>
<html lang="pt-BR">
<head>
    
    <?php include_once ROOT. "/vendor/head_css.php";?>
    <title>Financeiro</title>

</head>

<body>

    <?php include_once  ROOT . "/sistema/painelmobile.php"; ?>

    <div class="d-flex">

        <?php include_once  ROOT . "/sistema/painel.php"; ?>

        <div class="container-fluid">
            <div class="row ">
                <div class="col-lg-10 d-none d-md-none d-lg-block pr-0 pl-0 ts-bgAplicativos">
                    <ul class="nav a" id="myTabs">

                        <?php
                        $tab = '';

                        if (isset($_GET['tab'])) {
                            $tab = $_GET['tab'];
                        }
                        ?>
                        <?php if ($nivelMenu == 5) {
                            if ($tab == '') {
                                $tab = 'contasreceber';
                            } ?>
                            <li class="nav-item mr-1 ">
                                <a class="nav-link 
                                <?php if ($tab == "contasreceber") {echo " active ";} ?>" 
                                href="?tab=contasreceber" role="tab">Contas à Receber</a>
                            </li>
                        <?php }
                        if ($nivelMenu == 5) { ?>
                            <li class="nav-item mr-1 ">
                                <a class="nav-link 
                                <?php if ($tab == "gerenciarrecebimentos") {echo " active ";} ?>" 
                                href="?tab=gerenciarrecebimentos" role="tab">Gerenciar Recebimentos</a>
                            </li>
                        <?php }
                        if ($nivelMenu == 5) { ?>
                            <li class="nav-item mr-1 ">
                                <a class="nav-link 
                                <?php if ($tab == "contaspagar") {echo " active ";} ?>" 
                                href="?tab=contaspagar" role="tab">Contas à Pagar</a>
                            </li>
                        <?php }
                        if ($nivelMenu == 5) { ?>
                            <li class="nav-item mr-1 ">
                                <a class="nav-link 
                                <?php if ($tab == "gerenciarpagamentos") {echo " active ";} ?>" 
                                href="?tab=gerenciarpagamentos" role="tab">Gerenciar Pagamentos</a>
                            </li>
                        <?php }
                        if ($nivelMenu == 5) { ?>
                            <li class="nav-item mr-1 ">
                                <a class="nav-link 
                                <?php if ($tab == "dashboard") {echo " active ";} ?>" 
                                href="?tab=dashboard" role="tab">Dashboard</a>
                            </li>
                        <?php }
                        if ($nivelMenu == 5) { ?>
                            <li class="nav-item mr-1 ">
                                <a class="nav-link 
                                <?php if ($tab == "configuracao") {echo " active ";} ?>" 
                                href="?tab=configuracao" role="tab" data-toggle="tooltip" data-placement="top" title="Configurações"><i class="bi bi-gear"></i> Configurações</a>
                            </li>
                        <?php } ?>

                    </ul>
                </div>
                <!--Essa coluna só vai aparecer em dispositivo mobile-->
                <div class="col-7 col-md-9 d-md-block d-lg-none ts-bgAplicativos">
                <!--atraves do GET testa o valor para selecionar um option no select-->
                <?php if(isset($_GET['tab'])){
                    $getTab = $_GET['tab'];
                }else{
                    $getTab = '';
                }?>
                    <select class="form-select mt-2 ts-selectSubMenuAplicativos" id="subtabFinanceiro">
                        <option value="<?php echo URLROOT ?>/financeiro/?tab=contasreceber"
                        <?php if ($getTab == "contasreceber") {echo " selected ";} ?>>Contas à Receber</option>

                        <option value="<?php echo URLROOT ?>/financeiro/?tab=gerenciarrecebimentos"
                        <?php if ($getTab == "gerenciarrecebimentos") {echo " selected ";} ?>>Gerenciar Recebimentos</option>

                        <option value="<?php echo URLROOT ?>/financeiro/?tab=contaspagar" 
                        <?php if ($getTab == "contaspagar") {echo " selected ";} ?>>Contas à Pagar</option>

                        <option value="<?php echo URLROOT ?>/financeiro/?tab=gerenciarpagamentos"
                        <?php if ($getTab == "gerenciarpagamentos") {echo " selected ";} ?>>Gerenciar Pagamentos</option>

                        <option value="<?php echo URLROOT ?>/financeiro/?tab=dashboard" 
                        <?php if ($getTab == "dashboard") {echo " selected ";} ?>>Dashboard</option>

                        <option value="<?php echo URLROOT ?>/financeiro/?tab=configuracao" 
                        <?php if ($getTab == "configuracao") {echo " selected ";} ?>>Configurações</option>
                    </select>
                </div>
                
                <?php include_once  ROOT . "/sistema/novoperfil.php"; ?>

            </div><!--row-->
         
            <?php

            $src = "";
            $title = "Financeiro";

            if ($tab == "contasreceber") {
                $src = "consultas/contasreceber.php";
                $title = "Financeiro/Contas à Receber";
            }
            if ($tab == "gerenciarrecebimentos") {
                $src = "consultas/gerenciarrecebimentos.php";
                $title = "Financeiro/Gerenciar Recebimentos";
            }
            if ($tab == "contaspagar") {
                $src = "consultas/contaspagar.php";
                $title = "Financeiro/Contas à Pagar";
            }
            if ($tab == "gerenciarpagamentos") {
                $src = "consultas/gerenciarpagamentos.php";
                $title = "Financeiro/Gerenciar Pagamentos";
            }
            if ($tab == "dashboard") {
                $src = "consultas/dashboard.php";
                $title = "Financeiro/Dashboard";
            }
            if ($tab == "configuracao") {
                $src = "configuracao/configuracao.php";
                $title = "Financeiro/Configurações";
            }
            if ($tab == "configuracao") {
                $src = "configuracao/";
                $title = "Financeiro/Configuração";
                if (isset($_GET['stab'])) {
                    $src = $src . "?stab=" . $_GET['stab'];
                }
            }
            if ($src !== "") { ?>
                <div class="container-fluid p-0 m-0">
                    <iframe class="row p-0 m-0 ts-iframe"  src="<?php echo URLROOT ?>/financeiro/<?php echo $src ?>"></iframe>
                </div>
            <?php } ?>

        </div><!-- div container -->
    </div><!-- div class="d-flex" -->


    <!-- LOCAL PARA COLOCAR OS JS -->

    <?php include_once ROOT. "/vendor/footer_js.php";?>

    <script src="<?php echo URLROOT ?>/sistema/js/mobileSelectTabs.js"></script>

    <!-- LOCAL PARA COLOCAR OS JS -FIM -->

</body>

</html>