<div class="header">
    <hr style="top: 55px; color:#1B569D">
    <h1 style="position: absolute; top: 10px; font-size: 16px; text-align: left; text-justify: center; color:#000000">CONDICIONES ACTUALES DE ALMACENAMIENTO</h1>
    <img style="position: absolute;  width:90px ; height: 80px; float: right; top: 5px " src="<?php echo $logo_combinado ?>" />
    <h1 style="position: absolute; top: 10px; font-size: 16px; font-style: italic;text-align: right; text-justify: center; color:#1B569D">PLAN DE RECUPERACIÓN DE FUENTES HÍDRICAS</h1>
</div>

<div style="position: absolute; top: 70px; left: 20px; font-size: 18px; color:#000000;"><b>Bajo (< 30 %)</b>

            <table>
                <tr>
                    <th class="text-celd">EMBALSE</th>
                    <th class="text-celd">VOL. DISP. (HM3)</th>
                    <th class="text-celd">HIDROLÓGICA</th>
                </tr>

                <?php
                $j = 0;
                $cuenta = 0;
                while ($j < count($embalses_condiciones[0])) {
                    $cuenta++; ?>
                    <tr>
                        <td class="text-celd" style="font-size: 12px;"><?php echo $embalses_condiciones[0][$j][0]; ?> </td>
                        <td class="text-celd" style="font-size: 12px;"><?php echo $embalses_condiciones[0][$j][1]; ?></td>
                        <td class="text-celd" style="font-size: 12px;"><?php echo $embalses_condiciones[0][$j][2]; ?> </td>
                    </tr>

                <?php
                    $j++;
                }
                ?>

                <tr>
                    <td class="text-celd total"><b> TOTAL </b></td>
                    <td class="text-celd total" colspan="2"><b></b> <?php echo $cuenta . " "; ?>Embalses<?php echo " (" . number_format(($cuenta * 100 / count($datos_embalses)), 2, ",", ".") . "%)" ?></td>
                </tr>
            </table>


            <div style="font-size: 18px; color:#000000;  margin-top: 40px;"><b>Normal Bajo (30 % < A < 60%)</b>
            </div>

            <table>
                <tr>
                    <th class="text-celd">EMBALSE</th>
                    <th class="text-celd">VOL. DISP. (HM3)</th>
                    <th class="text-celd">HIDROLÓGICA</th>
                </tr>
                <?php
                $j = 0;
                $cuenta = 0;
                while ($j < count($embalses_condiciones[1])) {
                    $cuenta++; ?>
                    <tr>
                        <td class="text-celd" style="font-size: 12px;"><?php echo $embalses_condiciones[1][$j][0]; ?> </td>
                        <td class="text-celd" style="font-size: 12px;"><?php echo $embalses_condiciones[1][$j][1]; ?></td>
                        <td class="text-celd" style="font-size: 12px;"><?php echo $embalses_condiciones[1][$j][2]; ?> </td>
                    </tr>

                <?php
                    $j++;
                }
                ?>
                <tr>
                    <td class="text-celd total"><b> TOTAL </b></td>
                    <td class="text-celd total" colspan="2"><b></b> <?php echo $cuenta . " "; ?>Embalses<?php echo " (" . number_format(($cuenta * 100 / count($datos_embalses)), "2", ",", ".") . "%)" ?></td>
                </tr>
            </table>

            <!-- <div class="box-note"> Nota:</div> -->

</div>



<div style="font-size: 18px; color:#000000; position: absolute;  margin-top: 70px; margin-left: 560px;"><b>Normal Alto (60 % < A < 90 %) </b>

            <table>
                <tr>
                    <th class="text-celd">EMBALSE</th>
                    <th class="text-celd">VOL. DISP. (HM3)</th>
                    <th class="text-celd">HIDROLÓGICA</th>
                </tr>
                <?php
                $j = 0;
                $cuenta = 0;
                while ($j < count($embalses_condiciones[2])) {
                    $cuenta++; ?>
                    <tr>
                        <td class="text-celd" style="font-size: 12px;"><?php echo $embalses_condiciones[2][$j][0]; ?> </td>
                        <td class="text-celd" style="font-size: 12px;"><?php echo $embalses_condiciones[2][$j][1]; ?></td>
                        <td class="text-celd" style="font-size: 12px;"><?php echo $embalses_condiciones[2][$j][2]; ?> </td>
                    </tr>

                <?php
                    $j++;
                }
                ?>
                <tr>
                    <td class="text-celd total"><b> TOTAL </b></td>
                    <td class="text-celd total" colspan="2"><b></b> <?php echo $cuenta . " "; ?>Embalses<?php echo " (" . number_format(($cuenta * 100 / count($datos_embalses)), "2", ",", ".") . "%)" ?></td>
                </tr>
            </table>

</div>