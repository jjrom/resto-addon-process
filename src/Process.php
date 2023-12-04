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
    public $version = '1.1.0';

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

            $results = $this->context->dbDriver->fetch($this->context->dbDriver->pQuery('INSERT INTO ' . $this->context->dbDriver->commonSchema . '.process (id, userid, created, title, description, input) VALUES ($1,$2, now(),$3,$4,$5) ON CONFLICT (id) DO NOTHING RETURNING id', array(
                $body['id'],
                $this->user->profile['id'],
                $body['title'] ?? null,
                $body['description'] ?? null,
                $input
            )));
            if (count($results) !== 1) {
                throw new Exception(409, 'Process already exist ' . $body['id']);
            }

        } catch (Exception $e) {
            return RestoLogUtil::httpError($e->getCode(), $e->getMessage());
        }

        return RestoLogUtil::success('Process created', array(
            'id' => (integer)$results[0]['id']
        ));

    }

    /**
     *  @OA\Get(
     *      path="/processes",
     *      summary="Retrieve the list of available processes",
     *      description="The list of processes contains a summary of each process the OGC API - Processes offers, including the link to a more detailed description of the process",
     *      tags={"process"},
     *      @OA\Response(
     *          response="200",
     *          description="process",
     *          @OA\JsonContent(
     *              @OA\Property(
     *                  property="processes",
     *                  type="array",
     *                  description="Array of processes",
     *                  @OA\Items(ref="#/components/schemas/Process")
     *              )
     *          )
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
    public function getProcesses($params)
    {
        
        $results = $this->context->dbDriver->fetch($this->context->dbDriver->query('SELECT id, status, title, description, userid as owner, to_iso8601(created) as created, to_iso8601(finished) as finished, input, output FROM ' . $this->context->dbDriver->commonSchema . '.process');

        if (isset($results) && count($results) === 1) {
            return $this->formatProcess($results[0]);
        }
        
        return RestoLogUtil::httpError(404);
    }


    /**
     *  @OA\Get(
     *      path="/processes/{processId}",
     *      summary="Retrieve the process description",
     *      description="The process description contains information about inputs and outputs and a link to the execution-endpoint for the process.",
     *      tags={"process"},
     *      @OA\Parameter(
     *         name="processId",
     *         in="path",
     *         required=true,
     *         description="process's identifier",
     *         @OA\Schema(
     *             type="string"
     *         )
     *      ),
     *      @OA\Response(
     *          response="200",
     *          description="process",
     *          @OA\JsonContent(
     *              ref="#/components/schemas/Process"
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
        
        $results = $this->context->dbDriver->fetch($this->context->dbDriver->pQuery('SELECT id, status, title, description, userid as owner, to_iso8601(created) as created, to_iso8601(finished) as finished, input, output FROM ' . $this->context->dbDriver->commonSchema . '.process WHERE id=($1)', array(
            $params['processId']
        )));

        if (isset($results) && count($results) === 1) {
            return $this->formatProcess($results[0]);
        }
        
        return RestoLogUtil::httpError(404);
    }

    /**
     *  @OA\Get(
     *      path="/jobs",
     *      summary="Retrieve the list of jobs",
     *      description="Lists available jobs.",
     *      tags={"process"},
     *      @OA\Response(
     *          response="200",
     *          description="process",
     *          @OA\JsonContent(
     *              @OA\Property(
     *                  property="jobs",
     *                  type="array",
     *                  description="Array of jobs",
     *                  @OA\Items(ref="#/components/schemas/Job")
     *              )
     *          )
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
    public function getJobs($params)
    {
        
    }

    /**
     *  @OA\Get(
     *      path="/jobs/{jobId}",
     *      summary="Get job",
     *      description="Shows the status of a job.",
     *      tags={"process"},
     *      @OA\Parameter(
     *         name="jobId",
     *         in="path",
     *         required=true,
     *         description="job's identifier",
     *         @OA\Schema(
     *             type="string"
     *         )
     *      ),
     *      @OA\Response(
     *          response="200",
     *          description="process",
     *          @OA\JsonContent(
     *              ref="#/components/schemas/Job"
     *          )
     *      ),
     *      @OA\Response(
     *          response="400",
     *          description="Invalid *jobId*",
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
    public function getJob($params)
    {

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
