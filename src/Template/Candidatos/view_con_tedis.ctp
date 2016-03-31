<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Html->link(__('New Candidato'), ['action' => 'add']) ?></li>
        <li><?= $this->Html->link(__('List Clasificados'), ['controller' => 'Clasificados', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New Clasificado'), ['controller' => 'Clasificados', 'action' => 'add']) ?></li>
    </ul>
</nav>
<div class="candidatos index large-9 medium-8 columns content">
    <h3><?= __('Candidatos ESTRUCTURALES CONOCIDOS') ?></h3>
    
     <table cellpadding="0" cellspacing="0">
        <thead>
            <tr>
                <td>dni</td>
                <td>nombre</td>
                <td>apellidos</td>
                <td>expediente</td>
                <td>estudios</td>
                <td>TEDIS</td>
                <td>EDIS</td>
                
                
            </tr>
        </thead>

        <tbody>

            <?php foreach ($candidatos_conocidos as $candidato): ?>

                <?php 

                    $edu='';
                    switch ($candidato['estudios']) {
                        
                        case '':
                            $edu="<span class='glyphicon glyphicon-question-sign'></span> ";
                            break;
                        case 'ANALFABETOS          ':
                            $edu="<span class='glyphicon glyphicon-text-background'></span> ";
                            break;
                        case 'SIN ESTUDIOS         ':
                            $edu="<span class='glyphicon glyphicon-education'></span> ";
                            break;
                        case 'ESTUDIOS PRIMARIOS':
                            $edu="<span class='glyphicon glyphicon-education'></span><span class='glyphicon glyphicon-education'></span>";
                            break;
                        case 'EDUCACION SECUNDARIA OBLIGATORIA/GARANTIA SOCIAL':
                            $edu="<span class='glyphicon glyphicon-education'></span><span class='glyphicon glyphicon-education'></span><span class='glyphicon glyphicon-education'></span>";
                            break;
                         case 'BACHILLER/FP GRADO MEDIO':
                            $edu="<span class='glyphicon glyphicon-education'></span><span class='glyphicon glyphicon-education'></span><span class='glyphicon glyphicon-education'></span><span class='glyphicon glyphicon-education'></span>";
                            break;
                        case 'UNIVERSITARIO/FP GRADO SUPERIOR':
                            $edu="<span class='glyphicon glyphicon-education'></span><span class='glyphicon glyphicon-education'></span><span class='glyphicon glyphicon-education'></span><span class='glyphicon glyphicon-education'></span><span class='glyphicon glyphicon-education'></span>";
                            break;
                        
                        default:
                            // code...
                            break;
                    }

                 ?>

            <tr>
                <td><?= $candidato['dni']; ?></td>
                <td><?= $candidato['nombre']; ?></td>
                <td><?= $candidato['apellidos']; ?></td>
                <td><?= $candidato['expediente']; ?></td>
                <td><?= $edu; ?></td>
                <td><?= $candidato['tedis']; ?></td>
                <td><?= $candidato['numedis']; ?></td>
                
            </tr>

            <?php endforeach; ?>

        </tbody>
     </table>
</div>

<div class="candidatos index large-9 medium-8 columns content">
    <h3><?= __('Candidatos ESTRUCTURALES DESCONOCIDOS') ?></h3>
    
     <table cellpadding="0" cellspacing="0">
        <thead>
            <tr>
                <td>dni</td>
                <td>nombre</td>
                <td>apellidos</td>
                <td>expediente</td>
                <td>estudios</td>
                
                
            </tr>
        </thead>

        <tbody>

            <?php foreach ($candidatos_desconocidos as $candidato): ?>

            <tr>
                <td><?= $candidato['dni']; ?></td>
                <td><?= $candidato['nombre']; ?></td>
                <td><?= $candidato['apellidos']; ?></td>
                <td><?= $candidato['expediente']; ?></td>
                <td><?= $candidato['estudios']; ?></td>
                
            </tr>

            <?php endforeach; ?>

        </tbody>
     </table>
</div>
