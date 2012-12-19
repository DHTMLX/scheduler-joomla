# dhtmlxScheduler Joomla component

##Component structure
*  admin/ - back-end part of component
*  site/ - font-end part of component
*  install.xml - installation xml description

All scheduler logic is located in site/codebase.  
It includes scheduler codebase, scheduler configurator, code generator.
There are 3 main points in code:  
*  initializing admin panel  
     +  joomla/admin/views/schedulers/tmpl/default.php - admin form initializing  
     +  joomla/admin/controller.php - loading/saving configuration  
*  generating scheduler code  
     +  joomla/site/models/scheduler.php (method getScheduler())  
*  processing loading/saving requests  
     +  joomla/site/controller.php (method loadxml())  


##Database structure  
*  events_rec - stores scheduler events  
      +  event\_id - event id  
      +  start\_date - event start date/time  
      +  end\_date - event end date/time  
      +  text - event text  
      +  rec\_type - event recurring logic  
      +  event\_pid - parent event (for editing one event from recurring series)  
      +  event\_length - event length (required for recurring events)  
      +  user - event creator/editor id  
      +  lat - event latitude (for map view)  
      +  lng - event longuitude (for map view)  
*  scheduler_options - stores scheduler configuration  
      +  id - option id  
      +  name - option name  
      +  value - option value  
Here is a list of options:  
      +  scheduler\_xml - scheduler xml configuration. Is used to load settings in admin panel  
      +  scheduler\_php - parsed xml configuration and serialized into more useful form  
      +  scheduler\_php\_version - version of the last php configuration  
      +  scheduler\_xml\_version - version of the last xml configuration  
      +  scheduler\_stable\_config - last stable configuration (is used for restoring scheduler configuration after config xml error)  


##Codebase details
Core provides universal control panel, API for generating scheduler and events data-feed.  
Logic of parsing settings may be found in codebase/dhtmlxSchedulerConfigurator.php.  

For saving/parsing configuration is used the follow logic:  
when user saves configuration it is saved into database.  
At the same time scheduler\_xml\_version (have a look database structure) is increased.  
When user opens scheduler then configurators compare scheduler\_xml\_version and scheduler\_php\_version.  
If scheduler\_xml\_version if bigger than scheduler\_php\_version then it parses xml configuration and serialize it into php (scheduler\_php in database).  
At the same time configurator updates scheduler_php_version to actual.  
If scheduler\_xml\_version equals scheduler\_php\_version than xml is already parsed and serialized php configuration may be used.  