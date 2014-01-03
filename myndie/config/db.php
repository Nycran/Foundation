<?php
    use RedBean_Facade as R;
    R::setup('mysql:host=localhost;dbname=foundation','simbqa','agilorandandy'); 
    
    // Enable Bean Loading
    RedBean_Plugin_Cooker::enableBeanLoading(true);     
