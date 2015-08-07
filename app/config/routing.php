dog:
    resource: "@DogBundle/Resources/config/routing.yml"
    prefix:   /dogs/

cloudy_crud:
    resource: "@CloudyCrudBundle/Resources/config/routing.yml"
    prefix:   /cloudy/

app:
    resource: @AppBundle/Controller/
    type:     annotation
    
apps:
    path: /lol
    defaults: { _controller: AppBundle:Default:lol }
    
fos_user_security:
    resource: "@FOSUserBundle/Resources/config/routing/security.xml"

fos_user_profile:
    resource: "@FOSUserBundle/Resources/config/routing/profile.xml"
    prefix: /dogs/profile

fos_user_register:
    resource: "@FOSUserBundle/Resources/config/routing/registration.xml"
    prefix: /dogs/register

fos_user_resetting:
    resource: "@FOSUserBundle/Resources/config/routing/resetting.xml"
    prefix: /dogs/resetting

fos_user_change_password:
    resource: "@FOSUserBundle/Resources/config/routing/change_password.xml"
    prefix: /dogs/profile