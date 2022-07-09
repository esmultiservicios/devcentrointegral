<?php
$medidaTicket = 180;

?>
<!DOCTYPE html>
<html>

<head>

    <style>
        * {
            font-size: 12px;
            font-family: 'Times New Roman';
            margin: 3;
            padding: 3;			
        }

        h1 {
            font-size: 18px;
			display: inline-block;
			display: block;
			padding: 3px;
			margin-bottom: 5px;
        }

        .ticket {
            margin: 2px;
        }

        td,
        th,
        tr,
        table {
            border-top: 1px solid black;
            border-collapse: collapse;
        }

        td.precio {
			width: 20%;
			max-width: 20%;
			word-break: break-all;
        }

        td.cantidad {
			width: 10%;
			max-width: 10%;
			word-break: break-all;			
            font-size: 11px;
        }

        td.producto {
			width: 70%;
			max-width: 70%;			
            text-align: center;
        }

        th {
            text-align: center;
        }

        .centrado {
            text-align: center;
            align-content: center;
        }

        .ticket {
			width: 95%;
			max-width: 95%;
            margin: 3;
            padding: 3;
			word-break: break-all;			
        }

        img {
            max-width: inherit;
            width: inherit;
        }

        body {
            text-align: center;
        }		
    </style>
</head>

<body>
    <div class="ticket centrado">
        <h1><?php echo $consulta_registro['empresa']; ?></h1>
		<h2>Reporte Cierre de Caja</h2>
		<h2>Usuario: <?php echo $consulta_registro['usuario']; ?></h2>
		<h2>Fecha: <?php echo date("d/m/Y"); ?></h2>
        <table>
            <thead>
                <tr class="centrado">
                    <th class="cantidad">N°</th>
                    <th class="producto">Factura</th>
                    <th class="precio">Importe</th>
                </tr>
            </thead>
            <tbody>
				<?php
					$total = 0;					
					$fila = 1;
					while($registro_detalles = $result_factura_detalle->fetch_assoc()){
						$total += $registro_detalles["importe"];
						
						echo '
						  <tr>
							<td>'.$fila.'</td>
							<td class="textleft">'.$registro_detalles["factura"].'</td>
							<td class="textright">L. '.number_format($registro_detalles["importe"],2).'</td>						
						  </tr>
						';
						$fila++;
					}
				?>
            </tbody>
            <tr>
                <td class="cantidad"></td>
                <td class="producto">
                    <strong>TOTAL</strong>
                </td>
                <td class="precio">
                    L. <?php echo number_format($total, 2) ?>
                </td>
            </tr>
        </table>
    </div>
</body>

</html>