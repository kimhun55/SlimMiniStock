<?php

use Slim\App;
use Slim\Http\Request;
use Slim\Http\Response;
use \Firebase\JWT\JWT;


return function (App $app) {
    $container = $app->getContainer();



    $app->get('/', function (Request $request, Response $response, array $args) use ($container) {
        phpinfo();
    });

    $app->post('/login', function (Request $request, Response $response, array $args) {
 
        $input = $request->getParsedBody();
        $sql = "SELECT * FROM users WHERE email= :email";
        $sth = $this->db->prepare($sql);
        $sth->bindParam("email", $input['email']);
        $sth->execute();
        $user = $sth->fetchObject();
     
        // verify email address.
        if(!$user) {
            return $this->response->withJson(['error' => true, 'message' => 'These credentials do not match our records.']);  
        }
     
        // // verify password.
        // if (!password_verify($input['password'],$user->password)) {
        //     return $this->response->withJson(['error' => true, 'message' => 'These credentials do not match our records.']);  
        // }
     
        $settings = $this->get('settings'); // get settings array.
        
        $token = JWT::encode(['id' => $user->id, 'email' => $user->email], $settings['jwt']['secret'], "HS256");
     
        return $this->response->withJson(['token' => $token]);
     
    });


    $app->group('/api', function () use ($app) {

        $container = $app->getContainer();

        $app->get('/products', function (Request $request, Response $response, array $args) use ($container) {
           
            $sql = "SELECT * FROM products";
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            $product = $stmt->fetchAll();
            if (count($product)) {
                $input = [
                    'status' => 'success',
                    'message' => 'read product success',
                    'data' => $product
                ];
            } else {
                $input = [
                    'status' => 'fail',
                    'message' => 'empty product ',
                    'data' => $product
                ];
            }
            return $this->response->withJson($input);
        });



        $app->post('/productsby/{id}', function (Request $request, Response $response, array $args) use ($container) {
            //echo $args['id'];
            $sql = "SELECT * FROM products WHERE id ='$args[id]'";
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            $product = $stmt->fetchAll();

            $input = [
                'status' => 'success',
                'message' => 'read product success',
                'data' => $product
            ];


            return $this->response->withJson($input);
        });

        //add new product
        $app->post('/products', function (Request $request, Response $response, array $args) use ($container)
         {
            // รับจาก Client
            $body = $this->request->getParsedBody();
            // print_r($body);
            $img = "noimg.jpg";
            $sql = "INSERT INTO products(product_name,product_detail,product_barcode,product_price,product_qty,product_image) 
                       VALUES(:product_name,:product_detail,:product_barcode,:product_price,:product_qty,:product_image)";
           $sth = $this->db->prepare($sql);
           $sth->bindParam("product_name", $body['product_name']);
           $sth->bindParam("product_detail", $body['product_detail']);
           $sth->bindParam("product_barcode", $body['product_barcode']);
           $sth->bindParam("product_price", $body['product_price']);
           $sth->bindParam("product_qty", $body['product_qty']);
           $sth->bindParam("product_image", $img);

           if($sth->execute()){
               $data = $this->db->lastInsertId();
               $input = [
                   'id' => $data,
                   'status' => 'success'
               ];
           }else{
               $input = [
                   'id' => '',
                   'status' => 'fail'
               ];
           }

           return $this->response->withJson($input); 

         });

         // Edit Product  (Method Put)
         $app->put('/products/{id}', function (Request $request, Response $response, array $args) {
            // รับจาก Client
            $body = $this->request->getParsedBody();

            $sql = "UPDATE  products SET 
                           product_name=:product_name,
                           product_detail=:product_detail,
                           product_barcode=:product_barcode,
                           product_price=:product_price,
                           product_qty=:product_qty
                       WHERE id='$args[id]'";

           $sth = $this->db->prepare($sql);
           $sth->bindParam("product_name", $body['product_name']);
           $sth->bindParam("product_detail", $body['product_detail']);
           $sth->bindParam("product_barcode", $body['product_barcode']);
           $sth->bindParam("product_price", $body['product_price']);
           $sth->bindParam("product_qty", $body['product_qty']);
           

           if($sth->execute()){
               $data = $args['id'];
               $input = [
                   'id' => $data,
                   'status' => 'success'
               ];
           }else{
               $input = [
                   'id' => '',
                   'status' => 'fail'
               ];
           }

           return $this->response->withJson($input);  
         });

         // Delete Product  (Method Delete)
        $app->delete('/products/{id}', function (Request $request, Response $response, array $args) {
            // รับจาก Client
            $body = $this->request->getParsedBody();
            $sql = "DELETE FROM products WHERE id='$args[id]'";
 
            $sth = $this->db->prepare($sql);
            
            if($sth->execute()){
                $data = $args['id'];
                $input = [
                    'id' => $data,
                    'status' => 'success'
                ];
            }else{
                $input = [
                    'id' => '',
                    'status' => 'fail'
                ];
            }

            return $this->response->withJson($input); 
        });


    });// api/
};
