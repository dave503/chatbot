<?php

 require_once '../vendor/autoload.php';

 use BotMan\BotMan\BotMan;
 use BotMan\BotMan\BotManFactory;
 use BotMan\BotMan\Drivers\DriverManager;
 use BotMan\BotMan\Messages\Conversations\Conversation;
 use BotMan\BotMan\Messages\Outgoing\Question;
 use BotMan\BotMan\Messages\Outgoing\Actions\Button;
 use BotMan\BotMan\Messages\Incoming\Answer;
 use BotMan\BotMan\Cache\DoctrineCache;
 use Doctrine\Common\Cache\FilesystemCache;
 use BotMan\BotMan\Messages\Attachments\Image;
 use BotMan\BotMan\Messages\Outgoing\OutgoingMessage;

//Clase conversación
 class ConverNombre extends Conversation
 {

     public $name;

     public function askName()
     {  
         //Pregunta del chat bot al usuario donde se obtiene la respuesta
         $this->ask('Antes que nada. ¿Cómo te llamas? <br> Para dirigirme a ti', function(Answer $answer) {
             // Texto respuesta
             $this->name = $answer->getText();
             $this->say('Encantado, '.$this->name);
             $this->say('Que puedo hacer por ti?');
            //$this->askWhatToDo();
         });
     }
     public function run()
     {  
         // Función llamada cuando se inicia la conversación
         $this->askName();
     }
 }

 class ConverBye extends Conversation
 {
     public function askBye()
     {
         $question = Question::create('¿Solvente tus dudas?')
             ->fallback('Hoooo no!')
             ->addButtons([
                 Button::create('Si')->value('Me agrada saber eso!<br><a href ="http://www.ccsa.edu.sv/index.php/85-ingles-online-sm-info" target="_blank">Mas información</a>.<br><br>Quieres saber sobre nuestras palzas disponibles.<br><a href ="http://www.ccsa.edu.sv/index.php/empleo" target="_blank">Ver ofertas de empleo</a>.<br><img width="250" height="100" src="http://www.ccsa.edu.sv/images/logowebsite.png" alt="Logo">'),
                 Button::create('No!')->value('Gracias, poco a poco ire mejorando.<br><a href ="http://www.ccsa.edu.sv/index.php/85-ingles-online-sm-info" target="_blank">Mas información</a>.<br><img width="250" height="100" src="http://www.ccsa.edu.sv/images/logowebsite.png" alt="Logo">'),
             ]);
     
         $this->ask($question, function (Answer $answer) {
            
             if ($answer->isInteractiveMessageReply()) {
                 $value = $answer->getValue(); 
                 $text = $answer->getText();
                 if ($text == 'Si') {
                   $this->say($value);
                 }else{
                   $this->say($value);
                 }
             }
         });
     }
     public function run()
     {  
         // Función llamada cuando se inicia la conversación
         $this->askBye();
     }
 }

 $config = [
  // Your driver-specific configuration
  // "telegram" => [
  //    "token" => "TOKEN"
  // ]
  'web' => [
    'matchingData' => [
          'driver' => 'web',
      ],
  ],
];

 DriverManager::loadDriver(\BotMan\Drivers\Web\WebDriver::class);

//Se crea el objeto del chatbot. El parámatro de doctrine es el cache de las conversaciones y se indica en que directorio se almacenará dicha
 $botman = BotManFactory::create($config, new DoctrineCache(new FilesystemCache('/cache/')));

/*
 Inicio de la conversación por parte del usuario que puede ser: hola o buenas o buenos díaso bueno o dias o buenas tardes o buenas noches
*/
$botman->hears('.*(hola|buenas|buenos días|buenos dias|buenas tardes|buenas noches).*', function (BotMan $bot,$word) {
      //Se espera un segundo
      $bot->typesAndWaits(1);
      //Se inicia la conversación
      $bot->startConversation(new ConverNombre);
});

//Cuando el usuario escriba algo que no este en las preguntas
$botman->fallback(function($bot) {
 // $bot->reply('Lo siento!<br>No puedo entender eso.<br>Intenta otra pregunta.');
  $attachment = new Image('http://www.ccsa.edu.sv/images/logowebsite.png', [
    'custom_payload' => true,
]);
  // Build message object
  $message = OutgoingMessage::create('Lo siento!<br>No puedo entender eso.<br>Intenta otra pregunta.')->withAttachment($attachment);

  // Reply message object
  $bot->reply($message);
});
//Por si quiere ver la fecha xd
$botman->hears('fecha', function(BotMan $bot){
  date_default_timezone_set("america/el_salvador");
  $bot->reply(date('d/m/yy'));
  
});
//por si quiere saber la hora xd
$botman->hears('hora', function(BotMan $bot){
  date_default_timezone_set("america/el_salvador");
  $bot->reply(date('h:i a'));
  
});

//Pregunta 1
$botman->hears('.*(Que cursos ofrecen|cursos|Cursos|que cursos ofrecen|Que ofrecen|que ofrecen).*', function(BotMan $bot, $word){

  $bot->reply('Ofrecemos cursos de inglés, para niños jóvenes y adultos.<br>Tambien tenemos el curso de preparación TOEFL.<br><a href ="http://www.ccsa.edu.sv/index.php/85-ingles-online-sm-info" target="_blank">Mas información</a>.<br><a href ="http://www.ccsa.edu.sv/index.php/79-clases-on-line" target="_blank">Inscribirme</a>.');
  
});

//Pregunta 2
$botman->hears('.*(Donde estan ubicados|donde estan ubicados|ubicacion|Ubicacion|donde|Donde,|donde estan|Donde estan).*', function(BotMan $bot, $word){

    // Reply message object
    $bot->reply('En San Miguel<br>10a Avenida Norte y 10a Calle Oriente No. 609-D Bis, Barrio La Cruz, San Miguel.<br><br><iframe style="border: 0;" src="https://www.google.com/maps/embed?pb=!1m14!1m8!1m3!1d7759.598837882826!2d-88.17132000000001!3d13.486454000000002!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0xce4e57abde448969!2sCentro+Cultural+Salvadore%C3%B1o+Americano!5e0!3m2!1ses!2sus!4v1551306926047" width="260" height="200" frameborder="0" allowfullscreen="allowfullscreen"></iframe>');
});

//Pregunta 3
$botman->hears('.*(Niveles de los cursos|Niveles|Cuantos niveles tienen los cursos|niveles|cuantos niveles tienen los cursos).*', function(BotMan $bot, $word){

  $bot->reply('El curso para adultos, consta de un total de 20 niveles.');
});

//Pregunta 3
$botman->hears('.*(Precios|Aranceles|Cuento cuesta|Cuanto valen|cuantos cuesta|cuanto valen|precio|Precio|precios).*', function(BotMan $bot, $word){

  $bot->reply('El curso tiene una inversión de $10 de matrícula (ahora es gratis por la pandemia), más $50 del nivel a cursar ($37 ahora por la pandemia), más $39 de libros que se utilizan en varios niveles del curso.<br><a href ="http://www.ccsa.edu.sv/index.php/san-miguel/oferta-academica-sm" target="_blank">Mas información</a>');
});

//Pregunta 4
$botman->hears('.*(Cuento dura cada nivel|cuento dura cada nivel|duracion del nivel|Duracion del nivel|duracion de cada nivel|Duracion de cada nivel).*', function(BotMan $bot, $word){

  $bot->reply('Cada nivel tiene una duración de 1 mes en modalidad semanal y de 2 meses en modalidad de fines de semana.');
});

//Pregunta 5
$botman->hears('.*(Tienen cursos para niños|cursos para niños|tienen cursos para niños|tiene cursos para niños|Tiene cursos para niños|niños).*', function(BotMan $bot, $word){

  $bot->reply('<img width="260" height="200" src="http://www.ccsa.edu.sv/images/2019/03/01/ingles-ninos-sm.jpg" alt="Cursos de Ingles para Niños  (Presencial)"></span><div><h3>Cursos de Ingles para Niños  (Presencial)</h3><div><div>Cursos de inglés especializados para introducir a niños al idioma inglés mediante metodología lúdica y participativa con recursos didácticos de acuerdo a su edad.</div></div></div><br><a href ="http://www.ccsa.edu.sv/index.php/79-clases-on-line" target="_blank">Inscribirme</a>.');
  
});

//Pregunta 6
$botman->hears('.*(Tienen cursos para adultos|cursos para adultos|tienen cursos para adultos|tiene cursos para adultos|Tiene cursos para adultos|adultos).*', function(BotMan $bot, $word){

  $bot->reply('<img width="260" height="200" src="http://www.ccsa.edu.sv/images/2019/03/01/ingles-adultos-sm.jpg" alt="Niños  (Presencial)"></span><div><h3>Cursos de Ingles para Adultos desde 17 años  (Presencial)</h3><div><div>Programa de estudios del inglés en donde los participantes podrán obtener un nivel del idioma Inglés que les permita comunicarse efectivamente y sistemáticamente en situaciones de la vida real; profesional y académica.</div></div></div><br><a href ="http://www.ccsa.edu.sv/index.php/79-clases-on-line" target="_blank">Inscribirme</a>.');
  
});

//Pregunta 7
$botman->hears('.*(Tienen cursos para jovenes|cursos para jovenes|tienen cursos para jovenes|tiene cursos para jovenes|Tiene cursos para jovenes|jovenes).*', function(BotMan $bot, $word){

  $bot->reply('<img width="260" height="200" src="http://www.ccsa.edu.sv/images/2019/02/25/jovenes-13-16.jpg" alt="Niños  (Presencial)"></span><div><h3>Cursos de Ingles para Jóvenes 13 a 16 años (Presencial)</h3><div><div>Preparación del inglés que lleva a adolescentes a dominar el idioma inglés para que mediante este logre mayores oportunidades en el campo académico e incluso laboral.</div></div></div><br><a href ="http://www.ccsa.edu.sv/index.php/79-clases-on-line" target="_blank">Inscribirme</a>.');
  
});

//Pregunta 8
$botman->hears('.*(Ofrecen cursos para educacion basica|ofrecen cursos para educacion basica|Ofrecen cursos para educacion parvularia|ofrecen cursos para educacion parvularia|parvularia|Parvularia).*', function(BotMan $bot, $word){
  
  $bot->reply('No, únicamente ofrecemos tercer ciclo (7°-9°), bachillerato y cursos de inglés.<br><a href ="http://www.ccsa.edu.sv/index.php/san-miguel/oferta-academica-sm" target="_blank">Mas información</a>.<br><a href ="http://www.ccsa.edu.sv/index.php/79-clases-on-line" target="_blank">Inscribirme</a>.');
});

//Pregunta 9
$botman->hears('.*(Costo de bachillerato|costo de bachillerato|aranceles para bachillerato|Aranceles para bachillerato|bachillerato|Bachillerato).*', function(BotMan $bot, $word){
  
  $bot->reply('La inversión para bachillerato es de $250 de matrícula, más $92 de colegiatura (enero - noviembre).<br><a href ="http://www.ccsa.edu.sv/index.php/85-ingles-online-sm-info" target="_blank">Mas información</a>.');
});

//Pregunta 10
$botman->hears('.*(Costo de educacion basica|costo de educacion basica|aranceles para educacion basica|Aranceles para educacion basica|educacion basica|educacion basica|basica|costo de septimo|costo de octavo|costo de noveno|costo de 7|costo de 8|costo de 9).*', function(BotMan $bot, $word){

  $bot->reply('La inversión para Tercer ciclo de educacion basica es de $175 de matrícula, más $70 de colegiatura (enero - noviembre).<br><a href ="http://www.ccsa.edu.sv/index.php/85-ingles-online-sm-info" target="_blank">Mas información</a>.');
});

//Pregunta 11
$botman->hears('.*(Costo de TOEFL|cuanto cuesta el examen TOEFL|costo del examen TOEFL|TOEFL).*', function(BotMan $bot, $word){

  $bot->reply('El examen TOEFL tiene un costo de $60.<br><a href ="http://www.ccsa.edu.sv/index.php/85-ingles-online-sm-info" target="_blank">Mas información</a>.');
});

//Despedida
$botman->hears('.*(gracias|bye|adios|bueno|bueno gracias|bueno adios).*', function (BotMan $bot,$word) {
  //Se espera un segundo
  $bot->typesAndWaits(1);
  //Se inicia la conversación
  $bot->startConversation(new ConverBye);
  
});
// Botman empieza a escuchar
$botman->listen();

?>
