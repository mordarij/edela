<?php
namespace Acme\UserBundle\Security\Core;

use Acme\UserBundle\Entity\User;
use HWI\Bundle\OAuthBundle\OAuth\Response\UserResponseInterface;
use HWI\Bundle\OAuthBundle\Security\Core\User\FOSUBUserProvider as BaseClass;
use Symfony\Component\Security\Core\User\UserInterface;

class FOSUBUserProvider extends BaseClass
{

    /**
     * {@inheritDoc}
     */
    public function connect(UserInterface $user, UserResponseInterface $response)
    {
        $property = $this->getProperty($response);
        $username = $response->getUsername();

        //on connect - get the access token and the user ID
        $service = $response->getResourceOwner()->getName();

        $setter = 'set'.ucfirst($service);
        $setter_id = $setter.'Id';
        $setter_token = $setter.'AccessToken';

        //we "disconnect" previously connected users
        if (null !== $previousUser = $this->userManager->findUserBy(array($property => $username))) {
            $previousUser->$setter_id(null);
            $previousUser->$setter_token(null);
            $this->userManager->updateUser($previousUser);
        }

        //we connect current user
        $user->$setter_id($username);
        $user->$setter_token($response->getAccessToken());

        $this->userManager->updateUser($user);
    }

    /**
     * {@inheritdoc}
     */
    public function loadUserByOAuthUserResponse(UserResponseInterface $response,$email="")
    {
        $useremail = $response->getEmail(); 
		
        if($useremail=="") $useremail=$email;
        
		if($useremail!=""){
        	 $username = $useremail;
        }
        else $username = $response->getUsername();          

        /** @var User $user */
        $user = $this->userManager->findUserByUsernameOrEmail($username);
        
        $service = $response->getResourceOwner()->getName();
        $setterID = $service."Id";
        $setter = 'set'.ucfirst($service);
        $setter_id = $setter.'Id';
        $setter_token = $setter.'AccessToken';
        $getter = 'get'.ucfirst($service);
        $getter_id = $getter.'Id';
        //when the user is registrating
        if (null === $user) {
        	 if($this->userManager->findUserBy(array($setterID  => $response->getUsername()))!=null){
        	 	$user = $this->userManager->findUserBy(array($setterID  => $response->getUsername()));
        	 	return $user;
        	 }else{
           		 // create new user here
            $user = $this->userManager->createUser();
            $user->$setter_id($username);
            $user->$setter_token($response->getAccessToken());
            $user->setUsername($username);
            $user->setFullname($response->getRealName());
            if ($response->getResourceOwner()->getName() == 'facebook'){
				$user->setPhoto('https://graph.facebook.com/' .  $response->getUsername() . '/picture?type=large');
            } else {
                $user->setPhoto($response->getProfilePicture());
            }
            $user->setPassword($username);
            $user->setEnabled(true);
            if (filter_var($response->getEmail(), FILTER_VALIDATE_EMAIL)){
                $user->setEmail($response->getEmail());
                $user->setConfirmationToken(null);
            } else {
                $user->setEmail($username);
            }
            $this->userManager->updateUser($user);
            return $user;
        	}
        }else{
        	$user->$setter_id($response->getUsername());
        	$user-> $setter_token($response->getAccessToken());        	
         	$this->userManager->updateUser($user);
         	return $user;
        }		        
    }

}