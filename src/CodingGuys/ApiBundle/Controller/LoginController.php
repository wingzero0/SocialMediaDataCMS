<?php
/**
 * User: kit
 * Date: 03/01/16
 * Time: 2:07 PM
 */

namespace CodingGuys\ApiBundle\Controller;

use AppBundle\Controller\AppBaseController;
use AppBundle\Document\User;
use FOS\UserBundle\Event\FilterGroupResponseEvent;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;


/**
 * Api Login controller.
 *
 * @Route("/account")
 */
class LoginController extends AppBaseController
{
    /**
     * @ApiDoc(
     *  description="use refresh token to gen new token one",
     *  parameters={
     *      {"name"="refresh_token", "dataType"="string", "required"=true, "description"="account username"},
     *  }
     * )
     * @Route("/refreshToken", name="_refresh_token")
     * @Method("POST")
     */
    public function GetNewRefreshTokenAction(Request $request)
    {
        $refreshToken = $request->get('refresh_token');

        $params = array(
            'client_id' => $this->container->getParameter('oauth_client_id'),
            'client_secret' => $this->container->getParameter('oauth_client_secret'),
            'refresh_token' => $refreshToken,
            'grant_type' => 'refresh_token'
        );

        $result = $this->call($this->container->getParameter('token_url'), 'GET', $params);
        return new JSONResponse($result);

    }


    /**
     * @ApiDoc(
     *  description="login application, if success return access token, else return error",
     *  parameters={
     *      {"name"="username", "dataType"="string", "required"=true, "description"="account username"},
     *      {"name"="password", "dataType"="string", "required"=true, "description"="account password"},
     *  }
     * )
     * @Route("/login", name="_user_login")
     * @Method("POST")
     */
    public function LoginAction(Request $request)
    {
        $username = $request->get('username');
        $password = $request->get('password');
        if (!$username || !$password) {
            throw new \Exception('Something went wrong!');
        }
        $queryUser = $this->get('fos_user.user_manager')->findUserBy(array("username" => $username));

        if (!$queryUser) {
            throw new \Exception('Something went wrong!');
        }

        if ($queryUser instanceof User && !$queryUser->isLocked() && $queryUser->isEnabled()) {

            $params = array(
                'client_id' => $this->container->getParameter('oauth_client_id'),
                'client_secret' => $this->container->getParameter('oauth_client_secret'),
                'username' => $username,
                'password' => $password,
                'grant_type' => 'password',
            );

            $result = $this->call($this->container->getParameter('token_url'), 'GET', $params);

            return new JSONResponse($result);
        } else {
            if (!$queryUser->isEnabled()) {
                $jsonResponse = new JsonResponse(array("error" => "access_denied", "error_description" => "User account is disabled."));
                $jsonResponse->setStatusCode(401);
                return $jsonResponse;

            }
            if ($queryUser->isLocked()) {
                $jsonResponse = new JsonResponse(array("error" => "access_denied", "error_description" => "User account is locked."));
                $jsonResponse->setStatusCode(401);

                return $jsonResponse;
            }
            $jsonResponse = new JsonResponse(array("error" => "access_denied"));
            $jsonResponse->setStatusCode(401);

            return $jsonResponse;

        }

    }
    private function call($url, $method, $getParams = array(), $postParams = array())
    {
        ob_start();
        $curl_request = curl_init();

        curl_setopt($curl_request, CURLOPT_HEADER, 0); // don't include the header info in the output
        curl_setopt($curl_request, CURLOPT_RETURNTRANSFER, 1); // don't display the output on the screen
        $url = $url . "?" . http_build_query($getParams);
        switch (strtoupper($method)) {
            case "POST": // Set the request options for POST requests (create)
                curl_setopt($curl_request, CURLOPT_URL, $url); // request URL
                curl_setopt($curl_request, CURLOPT_POST, 1); // set request type to POST
                curl_setopt($curl_request, CURLOPT_POSTFIELDS, http_build_query($postParams)); // set request params
                break;
            case "GET": // Set the request options for GET requests (read)
                curl_setopt($curl_request, CURLOPT_URL, $url); // request URL and params
                break;
            case "PUT": // Set the request options for PUT requests (update)
                curl_setopt($curl_request, CURLOPT_URL, $url); // request URL
                curl_setopt($curl_request, CURLOPT_CUSTOMREQUEST, "PUT"); // set request type
                curl_setopt($curl_request, CURLOPT_POSTFIELDS, http_build_query($postParams)); // set request params
                break;
            case "DELETE":

                break;
            default:
                curl_setopt($curl_request, CURLOPT_URL, $url);
                break;
        }

        $result = curl_exec($curl_request); // execute the request
        if ($result === false) {
            $result = curl_error($curl_request);
        }
        curl_close($curl_request);
        ob_end_flush();

        return json_decode($result);
    }
}