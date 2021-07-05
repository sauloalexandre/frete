<table border="1">
    <tr>
        <td>CD</td>
        <td>CEP Origem</td>
        <td>CEP Destino</td>
        <td>Distancia</td>
        <td>Data/hora cadastro</td>
        <td>Data/hora atualizacao</td>
        <td>Editar</td>
        <td>Excluir</td>
    </tr>
    <?php foreach($list as $item) { ?>
        <tr>
            <td><?php echo $item->get("cd"); ?></td>
            <td><?php echo $item->get("origem"); ?></td>
            <td><?php echo $item->get("destino"); ?></td>
            <td><?php echo $item->get("distancia"); ?></td>
            <td><?php echo $item->get("dt_cadastro"); ?></td>
            <td><?php echo $item->get("dt_atualizacao"); ?></td>
            <td><a href="index.php?act=get&cd=<?php echo $item->get("cd"); ?>">E</a></td>
            <td><a href="index.php?act=exc&cd=<?php echo $item->get("cd"); ?>">X</a></td>
        </tr>
    <?php } ?>
</table>