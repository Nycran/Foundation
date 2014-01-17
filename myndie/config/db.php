<?php
    use RedBean_Facade as R;
    R::setup('mysql:host=localhost;dbname=foundation','root',''); 
    
    // Enable Bean Loading
    RedBean_Plugin_Cooker::enableBeanLoading(true);     
