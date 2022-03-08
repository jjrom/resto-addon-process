<?php
/*
 * Copyright (C) 2018 Jérôme Gasperi <jerome.gasperi@gmail.com>
 *
 * This file is subject to the terms and conditions defined in
 * file 'LICENSE.txt', which is part of this source code package.
 */

/**
 * Process management add-on
 */
class Process extends RestoAddOn
{

    /**
     * Add-on version
     */
    public $version = '1.0.0';

    /**
     * Constructor
     *
     * @param RestoContext $context
     * @param RestoUser $user
     */
    public function __construct($context, $user)
    {
        parent::__construct($context, $user);

        // Very important !
        $this->user->loadProfile();

    }

    /**
     *  @OA\Post(
     *      path="/processes",
     *      summary="Create process",
     *      description="Add a process",
     *      tags={"process"},
     *      @OA\Response(
     *          response="200",
     *          description="Acknowledgement of process creation",
     *          @OA\JsonContent(
     *              @OA\Property(
     *                  property="status",
     *                  type="string",
     *                  description="Status is *success*"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string",
     *                  description="Message information"
     *              ),
     *              @OA\Property(
     *                  property="id",
     *                  type="string",
     *                  description="process identifier"
     *              ),
     *              example={
     *                  "status": "success",
     *                  "message": "process created",
     *                  "id": 1234
     *              }
     *          )
     *      ),
     *      @OA\Response(
     *          response="403",
     *          description="Forbidden",
     *          @OA\JsonContent(ref="#/components/schemas/ForbiddenError")
     *      ),
     *      @OA\Response(
     *          response="400",
     *          description="Missing mandatory *id*",
     *          @OA\JsonContent(ref="#/components/schemas/BadRequestError")
     *      ),
     *      @OA\RequestBody(
     *         description="Process infos",
     *         required=true,
     *         type="object"
     *      ),
     *      security={
     *          {"basicAuth":{}, "bearerAuth":{}, "queryAuth":{}}
     *      }
     *  )
     *
     * @param array $params
     * @param array $body
     */
    public function addProcess($params, $body)
    {

        // Mandatory id
        if (!isset($body['id'])) {
            return RestoLogUtil::httpError(400, 'Missing mandatory "id"');
        }

        try {

            $input = isset($body['input']) ? json_encode($body['input'], JSON_UNESCAPED_SLASHES) : null;

            $results = $this->context->dbDriver->fetch($this->context->dbDriver->pQuery('INSERT INTO ' . $this->context->dbDriver->schema . '.process (id, userid, created, title, description, input) VALUES ($1,$2, now(),$3,$4,$5) ON CONFLICT (id) DO NOTHING RETURNING id', array(
                $body['id'],
                $this->user->profile['id'],
                $body['title'] ?? null,
                $body['description'] ?? null,
                $input
            )));
            if (count($results) !== 1) {
                throw new Exception(409, 'Process already exist ' . $body['id']);
            }

            // Launch process
            $this->triggerProcess($input);

        } catch (Exception $e) {
            return RestoLogUtil::httpError($e->getCode(), $e->getMessage());
        }

        return RestoLogUtil::success('Process created', array(
            'id' => (integer)$results[0]['id']
        ));

    }

    /**
     *  @OA\Get(
     *      path="/processes/{processId}",
     *      summary="Get process",
     *      description="Get process identified by *processId*",
     *      tags={"process"},
     *      @OA\Parameter(
     *         name="processId",
     *         in="path",
     *         required=true,
     *         description="process's identifier",
     *         @OA\Schema(
     *             type="integer"
     *         )
     *      ),
     *      @OA\Response(
     *          response="200",
     *          description="process",
     *          @OA\JsonContent(
     *              ref="#/components/schemas/OutputProcess"
     *          )
     *      ),
     *      @OA\Response(
     *          response="400",
     *          description="Invalid *processId*",
     *          @OA\JsonContent(ref="#/components/schemas/BadRequestError")
     *      ),
     *      @OA\Response(
     *          response="404",
     *          description="Not Found",
     *          @OA\JsonContent(ref="#/components/schemas/NotFoundError")
     *      )
     *  )
     *  
     *  @param array $params
     */
    public function getProcess($params)
    {
        
        $results = $this->context->dbDriver->fetch($this->context->dbDriver->pQuery('SELECT id, status, title, description, userid as owner, to_iso8601(created) as created, to_iso8601(finished) as finished, input, output FROM ' . $this->context->dbDriver->schema . '.process WHERE id=($1)', array(
            $params['processId']
        )));

        if (isset($results) && count($results) === 1) {
            return $this->formatProcess($results[0]);
        }
        
        return RestoLogUtil::httpError(404);
    }

    /**
     * Format raw process from database
     *
     * @param array $rawProcess
     */
    private function formatProcess($rawProcess)
    {
        $process = array(
            'id' => (integer) $rawProcess['id'],
            'status' => (integer) $rawProcess['status']
        );

        foreach (array_values(array('title', 'description', 'owner', 'created', 'finished', 'input', 'output')) as $key) {

            if (isset($rawProcess[$key])) {
                $process[$key] = $key === 'input' || $key === 'output' ? json_decode($rawProcess[$key], true) : $rawProcess[$key];
            }

        }

        return $process;
    }

    /**
     * Trigger a process
     * 
     * @param array $input
     */
    private function triggerProcess($input) 
    {

        if ( !isset($input) || !isset($input['endPoint ']) ) {
            // Nothing
        }

    }

}
