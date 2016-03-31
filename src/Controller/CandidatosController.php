<?php
namespace App\Controller;

use App\Controller\AppController;


/**
 * Candidatos Controller
 *
 * @property \App\Model\Table\CandidatosTable $Candidatos
 */
class CandidatosController extends AppController
{

    /**
     * Index method
     *
     * @return \Cake\Network\Response|null
     */
    public function index()
    {
        $this->paginate = [
            'contain' => ['Clasificados','Ceas'],

            
        ];
        $candidatos = $this->paginate($this->Candidatos);

        $this->set(compact('candidatos'));
        $this->set('_serialize', ['candidatos']);
    }

    /**
     * View method
     *
     * @param string|null $id Candidato id.
     * @return \Cake\Network\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $candidato = $this->Candidatos->get($id, [
            'contain' => ['Clasificados']
        ]);

        $this->set('candidato', $candidato);
        $this->set('_serialize', ['candidato']);
    }

    /**
     * Add method
     *
     * @return \Cake\Network\Response|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $candidato = $this->Candidatos->newEntity();
        if ($this->request->is('post')) {
            $candidato = $this->Candidatos->patchEntity($candidato, $this->request->data);
            if ($this->Candidatos->save($candidato)) {
                $this->Flash->success(__('The candidato has been saved.'));
                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('The candidato could not be saved. Please, try again.'));
            }
        }
        $clasificados = $this->Candidatos->Clasificados->find('list', ['limit' => 200]);
        $this->set(compact('candidato', 'clasificados'));
        $this->set('_serialize', ['candidato']);
    }

    /**
     * Edit method
     *
     * @param string|null $id Candidato id.
     * @return \Cake\Network\Response|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $candidato = $this->Candidatos->get($id, [
            'contain' => []
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $candidato = $this->Candidatos->patchEntity($candidato, $this->request->data);
            if ($this->Candidatos->save($candidato)) {
                $this->Flash->success(__('The candidato has been saved.'));
                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('The candidato could not be saved. Please, try again.'));
            }
        }
        $clasificados = $this->Candidatos->Clasificados->find('list', ['limit' => 200]);
        $this->set(compact('candidato', 'clasificados'));
        $this->set('_serialize', ['candidato']);
    }

    /**
     * Delete method
     *
     * @param string|null $id Candidato id.
     * @return \Cake\Network\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $candidato = $this->Candidatos->get($id);
        if ($this->Candidatos->delete($candidato)) {
            $this->Flash->success(__('The candidato has been deleted.'));
        } else {
            $this->Flash->error(__('The candidato could not be deleted. Please, try again.'));
        }
        return $this->redirect(['action' => 'index']);
    }

    public function viewPorExpediente($expediente = null)
    {
        
        $this->paginate = [
            'contain' => ['Clasificados'],
            'contain' => ['Ceas'],
            
        ];
        $candidatos = $this->paginate($this->Candidatos->findByExpediente($expediente));

        $this->set('candidatos', $candidatos);

        $this->set('_serialize', ['candidatos']);
    }

    public function viewPorCea($cea = null)
    {
        
        $this->paginate = [
            'contain' => ['Ceas'],
            'contain' => ['Clasificados'],
            
        ];
        $candidatos = $this->paginate($this->Candidatos->findByCea_id($cea));
        $ceas = $this->Candidatos->Ceas->get($cea);

        $this->set('candidatos', $candidatos);
        $this->set('ceas', $ceas['nombre']);
        $this->set('_serialize', ['ceas']);
    }

    public function viewConTedis()
    {
       
       $this->loadModel('Participantes');
       
       /* 
       ** Variables 
       */

            /* Listas */
       
       $lista_candidatos_conocidos = [];
       $lista_candidatos_desconocidos = [];
       $lista_participantes = [];
       $lista_dni_candidatos=[];
       $lista_dni_participantes=[];
       $lista_rgc_participantes=[];
       $lista_coyunturales = [];

       /*** 
       $lista_expedientes_asignados 
                            --> numero de RGC
                                --> tedis
                                --> numedis
        **/

       $lista_expedientes_asignados = [];


       $clasificado = "";

       /* Contadores */
       $clasificado_C = 0;
       $clasificado_E = 0;
       $i = 0;
       $d = 0;
       $c = 0;
       $h = 0;
       $exp_count = 0;
       //$candidatos = $this->paginate($this->Candidatos);
       //$participantes = $this->paginate($this->Participantes);

       
       /* 
       ** Full CONSULTAS a la base para las tablas.
       */


       $participantes = $this->Participantes
                        ->find()
                        //->select(['id', 'dni'])
                        ->all();

       $candidatos = $this->Candidatos
                        ->find()
                        //->where(['estudios' => 'ESTUDIOS PRIMARIOS'])
                        //->select(['id', 'dni'])
                        //->toArray()
                        ->all()
                        ;


        /* 
        **************************************
        ** Filtrados para generar listas... **
        **************************************
        */   
        

            /* 
            ** Listas de PARTICIPANTES DE LA BASE DE EDIS-LEON...
            */   

/* 1. Revisamos las personas de las que tenemos datos en EDIS: */
        
        foreach ($participantes as $participante) {
            
/* 
* 2. Creamos un arreglo con todos los 
* participantes y sus características.
*/ 

            $lista_participantes [$participante['dni']] =[
                                                    
                                                    'nombre'=>$participante ['nombre'],
                                                    'apellidos'=>$participante ['apellidos'],
                                                    'numedis'=>$participante ['numedis'], 
                                                    'numrgc'=>$participante ['numrgc'],
                                                    'tedis'=>$participante ['tedis']];
/* 
* Si conocemos el número de RGC lo añadimos a 
* $lista_expedientes_asignados asociando el 
* tedis y el numero de exp. edis.
*/            

            if ($participante['numrgc']) {
                 
                 $lista_expedientes_asignados [$participante['numrgc']]= ['tedis'=>$participante ['tedis'],
                                                                        'numedis' => $participante ['numedis']
                                                                            ];
             } 

/*
* Creamos una lista de dni que tenemos en la base y otra con los numeros de rgc.
*/            
           array_push($lista_dni_participantes, $participante ['dni']);
           array_push($lista_rgc_participantes, $participante ['numrgc']);

            $h++;
        }


            /* 
            ** Cruces con la Lista de CANDIDATOS PARA EL PLAN DE EMPLEO...
            */   

/* 1. Revisamos los candidatos: */

        foreach ($candidatos as $candidato) {

            
            switch ($candidato ['clasificado_id']) {
                
/* Elegimos los estructurales */

                case 1:

                    $clasificado = "Estructural";
                    $clasificado_E++;

/** 
* Si el candidato tiene un DNI
* que está en la $lista_dni_participantes
* lo añadimos a la $lista_candidatos_conocidos...
**/

                        if (in_array($candidato['dni'], $lista_dni_participantes)) {

                            $i++;
                            $lista_candidatos_conocidos [$candidato['dni']] = [
                                                    'id'=>$candidato['id'],
                                                    'dni'=>$candidato['dni'],
                                                    'expediente'=>$candidato ['expediente'],
                                                    'nombre'=>$candidato ['nombre'],
                                                    'apellidos'=>$candidato ['apellidos'],
                                                    'estudios'=>$candidato ['estudios'],
                                                    'tedis'=>$lista_participantes[$candidato['dni']]['tedis'],
                                                    'clasificado'=>$clasificado,
                                                    'numedis'=>$lista_participantes[$candidato['dni']]['numedis'],
                                                    ];
/**
* Si el candidato tiene un numero de expediente 
* de renta que no estaba en nuestra base EDIS lo añadimos
* a la $lista_expedientes_asignados. Además añadimos el
* numero de expediente a la $lista_rgc_participantes.
**/
                            if (!in_array($candidato['expediente'], $lista_expedientes_asignados)) {

                                $lista_expedientes_asignados [$candidato['expediente']]= ['tedis'=>$lista_participantes[$candidato['dni']]['tedis'],
                                                                                            'numedis' => $lista_participantes[$candidato['dni']]['numedis']
                                                                                            ];
                               
                               array_push($lista_rgc_participantes, $candidato['expediente']);

                                }

                        } else {

/**
* Si el dni del candidato NO está en la lista de participantes
* comprobamos si el numero de expediente de renta del candidato
* está en $lista_rgc_participantes (la lista de los números de
* expediente de nuestra base).
**/

                            if (in_array($candidato['expediente'], $lista_rgc_participantes)) {

                                //$candidato['expediente']." --> ".$lista_expedientes_asignados[$candidato['expediente']]['tedis'].'<br>';
                                $exp_count++;

                                $i++;

/**
* Si el número de expediente está en la
* $lista_rgc_participantes, añadimos al candidato a
* la $lista_candidatos_conocidos.
**/
                                $lista_candidatos_conocidos [$candidato['dni']] = [
                                                'id'=>$candidato['id'],
                                                'dni'=>$candidato['dni'],
                                                'expediente'=>$candidato ['expediente'],
                                                'nombre'=>$candidato ['nombre'],
                                                'apellidos'=>$candidato ['apellidos'],
                                                'estudios'=>$candidato ['estudios'],
                                                'tedis'=>$lista_expedientes_asignados [$candidato['expediente']]['tedis'],
                                                'clasificado'=>$clasificado,
                                                'numedis' => $lista_expedientes_asignados [$candidato['expediente']]['numedis']
                                                ];
                                               
                               
                            }else{

/**
* Si el número de expediente NO está en la
* $lista_rgc_participantes, añadimos al candidato 
* a la $lista_candidatos_desconocidos.
**/
                                $d++;
                                $lista_candidatos_desconocidos [$candidato['dni']] = [
                                                        'id'=>$candidato['id'],
                                                        'dni'=>$candidato['dni'],
                                                        'expediente'=>$candidato ['expediente'],
                                                        'nombre'=>$candidato ['nombre'],
                                                        'apellidos'=>$candidato ['apellidos'],
                                                        'estudios'=>$candidato ['estudios'],
                                                        //'tedis'=>$lista_participantes[$candidato['dni']]['tedis'],
                                                        'clasificado'=>$clasificado,
                                                        ]; 
                            }     
                        }

                    break;

                /*
                ***** En el caso de que sea COYUNTURAL:
                */

                case 2:
                    $clasificado = "Coyuntural";
                    $clasificado_C++;
                    break;
                
                default:
                  
                    break;
            }
            
        }


        //debug($candidatos);exit();
        //echo "Estructurales: ".$clasificado_E;
        //echo "<br>Coyunturales: ".$clasificado_C;
        //echo "<br>Expediente de renta en la base edis: ".$exp_count;
        
        //debug($lista_expedientes_edis);
        //echo "<br>ESTRUCTURALES CONOCIDOS (". $i.")";
        
        
        //debug($lista_rgc_participantes);
        //debug($lista_candidatos_conocidos);
        //echo "<br><br>ESTRUCTURALES DESCONOCIDOS (".$d.")";
        //debug($lista_candidatos_desconocidos);
        //echo "<br><br>Coyunturales (".$c.")";
        //debug($lista_coyunturales);
        //debug($lista_expedientes_asignados);

        //debug($lista_participantes[$candidato['dni']]);exit();

        
        //echo $i."<br>";
        //echo $h;
        //debug($lista_candidatos_desconocidos);
        //exit();

        $datos = [
                    //'candidatos' => $candidatos,
                    'candidatos_conocidos' => $lista_candidatos_conocidos,
                    'candidatos_desconocidos' => $lista_candidatos_desconocidos,
                    'cuenta_estructurales' => $clasificado_E,
                    'cuenta_coyunturales' => $clasificado_C,

                    ];
        
                    //debug($lista_candidatos_conocidos);exit();

        $this->set($datos);
        


        //$this->set('ceas', $ceas['nombre']);

        //debug($dni_candidatos);exit();
       
    }


}
