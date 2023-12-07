<?php
/*
 * Copyright (C) 2023 Jérôme Gasperi <jerome.gasperi@gmail.com>
 *
 * This file is subject to the terms and conditions defined in
 * file 'LICENSE.txt', which is part of this source code package.
 */

/**
 *
 *  @OA\Schema(
 *      schema="Process",
 *      required={"id", "version"},
 *      @OA\Property(
 *          property="id",
 *          type="string"
 *      ),
 *      @OA\Property(
 *          property="version",
 *          type="string"
 *      ),
 *      @OA\Property(
 *          property="title",
 *          type="string"
 *      ),
 *      @OA\Property(
 *          property="description",
 *          type="string"
 *      ),
 *      @OA\Property(
 *          property="keywords",
 *          type="array",
 *          @OA\Items(
 *              type="string",
 *          )
 *      ),
 *      @OA\Property(
 *          property="metadata",
 *          type="array",
 *          @OA\Items(
 *              type="object",
 *              @OA\Property(
 *                  property="title",
 *                  type="string"
 *              ),
 *              @OA\Property(
 *                  property="role",
 *                  type="string"
 *              ),
 *              @OA\Property(
 *                  property="href",
 *                  type="string"
 *              )
 *      ),
 *      @OA\Property(
 *          property="jobControlOptions",
 *          type="array",
 *          @OA\Items(
 *              type="string",
 *              enum={"sync-execute", "async-execute", "dismiss"}
 *          )
 *      ),
 *      @OA\Property(
 *          property="outputTransmission",
 *          type="array",
 *          @OA\Items(
 *              type="string",
 *              default="value",
 *              enum={"value", "reference"}
 *          )
 *      ),
 *      @OA\Property(
 *          property="links",
 *          type="array",
 *          @OA\Items(ref="#/components/schemas/Link")
 *      ),
 *      @OA\Property(
 *          property="inputs",
 *          type="object",
 *          @OA\JsonContent()
 *      ),
 *      @OA\Property(
 *          property="outputs",
 *          type="object",
 *          @OA\JsonContent()
 *      )
 *  )
 *
 *
 *  @OA\Schema(
 *      schema="Job",
 *      required={"jobID", "status", "type"},
 *      @OA\Property(
 *          property="jobID",
 *          type="string"
 *      ),
 *      @OA\Property(
 *          property="processID",
 *          type="string"
 *      ),
 *      @OA\Property(
 *          property="type",
 *          type="string",
 *          enum={"process"}
 *      ),
 *      @OA\Property(
 *          property="status",
 *          type="string",
 *          enum={"accepted", "running", "successful", "failed", "dismissed"}
 *      ),
 *      @OA\Property(
 *          property="message",
 *          type="string"
 *      ),
 *      @OA\Property(
 *          property="created",
 *          type="string"
 *      ),
 *      @OA\Property(
 *          property="started",
 *          type="string"
 *      ),
 *      @OA\Property(
 *          property="finished",
 *          type="string"
 *      ),
 *      @OA\Property(
 *          property="updated",
 *          type="string"
 *      ),
 *      @OA\Property(
 *          property="progress",
 *          type="integer",
 *          minimum=0,
 *          maximum=100
 *      ),
 *      @OA\Property(
 *          property="links",
 *          type="array",
 *          @OA\Items(ref="#/components/schemas/Link")
 *      )
 *  )
 *
 *  // https://github.com/opengeospatial/ogcapi-processes/blob/master/openapi/schemas/processes-dru/executionUnit.yaml
 *  @OA\Schema(
 *      schema="ApplicationPackage",
 *      required={"executionUnit"},
 *      @OA\Property(
 *          property="processDescription",
 *          @OA\Items(ref="#/components/schemas/Process")
 *      ),
 *      @OA\Property(
 *          property="executionUnit",
 *          type="object",
 *          @OA\JsonContent()
 *      )
 *  )
 * 
 * Process management add-on
 */
class Process extends RestoAddOn
{
    /**
     * Add-on version
     */
    public $version = '2.0.0';

    /*
     * OGC API Processes default version
     */
    private $apiVersion = '1.0.0';

    /* 
     * The root path for this API endpoint
     */
    private $landingRoot = '/oapi-p';

    /*
     * List of job/process columns to retrieve in the SQL query
     */
    private $jobColumns = 'id, userid as owner, process_id, type, status, message, to_iso8601(created) as created, to_iso8601(started) as started, to_iso8601(finished) as finished, to_iso8601(updated) as updated, progress, body, container_id';
    private $processColumns = 'id, userid as owner, title, description, version, keywords, to_iso8601(created) as created, to_iso8601(updated) as updated, content, execution_unit';
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
     * Landing page conforms to OGC API Processes
     * (see https://docs.ogc.org/is/18-062r2/18-062r2.html#toc23)
     *
     *    @OA\Get(
     *      path="/",
     *      summary="Landing page",
     *      description="Landing page for the OGC API Processes server. Should be used by client to automatically detects endpoints to processes API",
     *      tags={"Server"},
     *      @OA\Response(
     *          response="200",
     *          description="OGC API Processes landing page",
     *          @OA\JsonContent(
     *              @OA\Property(
     *                  property="title",
     *                  type="string"
     *              ),
     *              @OA\Property(
     *                  property="description",
     *                  type="string"
     *              ),
     *              @OA\Property(
     *                  property="links",
     *                  type="array",
     *                  @OA\Items(ref="#/components/schemas/Link")
     *              )
     *          )
     *      ),
     *      @OA\Response(
     *          response="404",
     *          description="Not found"
     *      )
     *    )
     */
    public function hello()
    {

        return array(
            'title' => 'OGC API Processes server',
            'description' => 'OGC API Processes server provided by resto engine (https://github.com/jjrom/resto)',
            'resto:version' => RestoConstants::VERSION,
            'links' => array_merge(
                array(
                    array(
                        'rel' => 'self',
                        'type' => RestoUtil::$contentTypes['json'],
                        'title' => getenv('API_INFO_TITLE'),
                        'href' => $this->context->core['baseUrl'] . $this->landingRoot
                    ),
                    array(
                        'rel' => 'service-desc',
                        'type' => RestoUtil::$contentTypes['openapi+json'],
                        'title' => 'OpenAPI 3.0 definition endpoint',
                        'href' => $this->context->core['baseUrl'] . $this->landingRoot . RestoRouter::ROUTE_TO_API
                    ),
                    array(
                        'rel' => 'http://www.opengis.net/def/rel/ogc/1.0/conformance',
                        'type' => RestoUtil::$contentTypes['json'],
                        'title' => 'Conformance declaration',
                        'href' => $this->context->core['baseUrl'] . $this->landingRoot . RestoRouter::ROUTE_TO_CONFORMANCE
                    ),
                    array(
                        'rel' => 'http://www.opengis.net/def/rel/ogc/1.0/processes',
                        'type' => RestoUtil::$contentTypes['json'],
                        'title' => 'Processes list',
                        'href' => $this->context->core['baseUrl'] . $this->landingRoot . '/processes'
                    ),
                    array(
                        'rel' => 'http://www.opengis.net/def/rel/ogc/1.0/job-list',
                        'type' => RestoUtil::$contentTypes['json'],
                        'title' => 'Jobs list',
                        'href' => $this->context->core['baseUrl'] . $this->landingRoot . '/jobs'
                    )

                )
            )
        );
    }

    /**
     *    @OA\Get(
     *      path="/conformance",
     *      summary="Conformance page",
     *      description="Returns the OGC API Processes conformance description as JSON document",
     *      tags={"Server"},
     *      @OA\Response(
     *          response="200",
     *          description="OGC API Processes conformance definition",
     *          @OA\JsonContent(
     *               @OA\Property(
     *                  property="conformsTo",
     *                  type="array",
     *                  description="Array of conformance specification urls",
     *                  @OA\Items(
     *                      type="string"
     *                  )
     *               )
     *          )
     *      ),
     *      @OA\Response(
     *          response="404",
     *          description="Not found"
     *      )
     *    )
     */
    public function conformance()
    {
        return array(
            'conformsTo' => array(
                'http://www.opengis.net/spec/ogcapi-processes-1/1.0/conf/core',
		        'http://www.opengis.net/spec/ogcapi-processes-1/1.0/conf/json',
		        'http://www.opengis.net/spec/ogcapi-processes-1/1.0/conf/oas30',
                'http://www.opengis.net/spec/ogcapi-processes-1/1.0/conf/job-list',
                'http://www.opengis.net/spec/ogcapi-processes-1/1.0/conf/ogc-process-description',
                'http://www.opengis.net/spec/ogcapi-processes-1/1.0/conf/dismiss',
                'http://www.opengis.net/spec/ogcapi-processes-2/1.0/req/deploy-replace-undeploy'
            )
        );
    }

    /**
     * Return API server definition as OpenAPI 3.0 document
     * (see https://docs.ogc.org/is/18-062r2/18-062r2.html#toc24)
     *
     *    @OA\Get(
     *      path="/api.{format}",
     *      summary="OpenAPI definition",
     *      description="Returns the server API definition as an OpenAPI 3.0 JSON document (default) or as an HTML page (if format is specified and set to *html*)",
     *      tags={"Server"},
     *      @OA\Parameter(
     *          name="format",
     *          in="path",
     *          description="Output format - *json* or *html*",
     *          required=true,
     *          @OA\Items(
     *              type="string",
     *              enum={"json", "html"}
     *          )
     *      ),
     *      @OA\Response(
     *          response="200",
     *          description="OpenAPI 3.0 definition"
     *      ),
     *      @OA\Response(
     *          response="404",
     *          description="Not found"
     *      )
     *    )
     */
    public function api()
    {
        try {
            $content = @file_get_contents('/docs/resto-processes-api.' . $this->context->outputFormat);
        } catch (Exception $e) {
            $content = false;
        }

        if ($content === false) {
            return RestoLogUtil::httpError(404);
        }

        if ($this->context->outputFormat === 'json') {
            $this->context->outputFormat = 'openapi+json';
            return json_decode($content, true);
        }

        /*
         * Set range and headers
         */
        header('HTTP/1.1 200 OK');
        header('Content-Type: ' . RestoUtil::$contentTypes[$this->context->outputFormat]);
        echo $content;

        return null;
    }

    /**
     *  @OA\Post(
     *      path="/processes",
     *      summary="Create process",
     *      description="Add a process",
     *      tags={"process"},
     *      @OA\Response(
     *          response="201",
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
     *         description="Process definition as an ApplicationPackage",
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/ApplicationPackage")
     *      ),
     *      security={
     *          {"basicAuth":{}, "bearerAuth":{}, "queryAuth":{}}
     *      }
     *  )
     *
     *  SQL table
     *
     *      "id"                    TEXT PRIMARY KEY,
     *      userid                  BIGINT,
     *      title                   TEXT
     *      description             TEXT,
     *      version                 TEXT,
     *      keywords                TEXT[],
     *      created                 TIMESTAMP,
     *      content                 JSON
     *
     * @param array $params
     * @param array $body
     */
    public function deploy($params, $body)
    {

        // Only admin can post process
        if ( !$this->user->hasGroup(RestoConstants::GROUP_ADMIN_ID) ) {
            return RestoLogUtil::httpError(403);
        }

        $internalProcess = $this->apToInternal($body);

        if ($this->processExists($internalProcess['id'])) {
            RestoLogUtil::httpError(409, 'Process ' . $internalProcess['id'] . ' already exist');
        }

        try {

            $results = $this->context->dbDriver->fetch($this->context->dbDriver->pQuery('INSERT INTO ' . $this->context->dbDriver->commonSchema . '.process (id, userid, title, description, version, keywords, content, execution_unit, created, updated) VALUES ($1, $2, $3, $4, $5, $6, $7, $8, now(), now()) ON CONFLICT (id) DO NOTHING RETURNING id', array(
                $internalProcess['id'],
                $this->user->profile['id'],
                $internalProcess['title'],
                $internalProcess['description'],
                $internalProcess['version'],
                isset($internalProcess['keywords']) ? '{' . join(',', $internalProcess['keywords']) . '}' : null,
                json_encode($internalProcess['content'], JSON_UNESCAPED_SLASHES),
                json_encode($internalProcess['executionUnit'], JSON_UNESCAPED_SLASHES)
            )));

            if (count($results) !== 1) {
                throw new Exception(500, 'Cannot create process ' . $internalProcess['id']);
            }

        } catch (Exception $e) {
            return RestoLogUtil::httpError($e->getCode(), $e->getMessage());
        }

        /*
         * [IMPORTANT] Specification requires HTTP 201 Created, not 200
         * (see https://github.com/opengeospatial/ogcapi-processes/blob/master/extensions/deploy_replace_undeploy/standard/sections/clause_6_deploy_replace_undeploy.adoc#deploying-a-new-process-to-the-api
         */
        $this->context->httpStatus = 201;

        return RestoLogUtil::success('Process created', array(
            'id' => $results[0]['id']
        ));

    }

    /**
     *  @OA\Put(
     *      path="/processes/{processId}",
     *      summary="Replace a process",
     *      description="Replace a process",
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
     *          response="204",
     *          description="Acknowledgement of process update",
     *      ),
     *      @OA\Response(
     *          response="403",
     *          description="Forbidden",
     *          @OA\JsonContent(ref="#/components/schemas/ForbiddenError")
     *      ),
     *      @OA\RequestBody(
     *         description="Process definition as an ApplicationPackage",
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/ApplicationPackage")
     *      ),
     *      security={
     *          {"basicAuth":{}, "bearerAuth":{}, "queryAuth":{}}
     *      }
     *  )
     * 
     * @param array $params
     * @param array $body
     */
    public function replace($params, $body)
    {

        $process = $this->getProcess($params);

        // Only the owner of the process or the admin can delete it
        if ($process['owner'] !== $this->user->profile['id']) {
            if (!$this->user->hasGroup(RestoConstants::GROUP_ADMIN_ID)) {
                return RestoLogUtil::httpError(403);
            }
        }
        
        $internalProcess = $this->apToInternal($body);

        if ( $internalProcess['id'] !== $params['processId'] ) {
            return RestoLogUtil::httpError(400, 'Process identifier does not match ressource ' . $params['processId']);
        }

        try {
            
            $this->context->dbDriver->fetch($this->context->dbDriver->pQuery('UPDATE ' . $this->context->dbDriver->commonSchema . '.process SET userid=$2, title=$3, description=$4, version=$5, keywords=$6, content=$7, execution_unit=$8, updated=now() WHERE id=$1', array(
                $internalProcess['id'],
                $this->user->profile['id'],
                $internalProcess['title'],
                $internalProcess['description'],
                $internalProcess['version'],
                isset($internalProcess['keywords']) ? '{' . join(',', $internalProcess['keywords']) . '}' : null,
                json_encode($internalProcess['content'], JSON_UNESCAPED_SLASHES),
                json_encode($internalProcess['executionUnit'], JSON_UNESCAPED_SLASHES)
            )));

        } catch (Exception $e) {
            return RestoLogUtil::httpError($e->getCode(), $e->getMessage());
        }

        /*
         * [IMPORTANT] Specification requires HTTP 204 No Content, not 200
         * (see https://github.com/opengeospatial/ogcapi-processes/blob/master/extensions/deploy_replace_undeploy/standard/sections/clause_6_deploy_replace_undeploy.adoc#deploying-a-new-process-to-the-api
         */
        $this->context->httpStatus = 204;

        return RestoLogUtil::success('Process updated', array(
            'id' => $internalProcess['id']
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

        $processes = array();

        try {

            $results = $this->context->dbDriver->query('SELECT ' . $this->processColumns . ' FROM ' . $this->context->dbDriver->commonSchema . '.process');

            while ($process = pg_fetch_assoc($results)) {
                $processes[] = $this->internalToProcess($process, array(
                    'showExecutionUnit' => false,
                    'showIO' => false
                ));
            }

        } catch (Exception $e) {
            return RestoLogUtil::httpError($e->getCode(), $e->getMessage());
        }

        return array(
            'processes' => $processes,
            'links' => array(
                array(
                    'rel' => 'up',
                    'type' => RestoUtil::$contentTypes['json'],
                    'href' => $this->context->core['baseUrl'] . $this->landingRoot
                ),
                array(
                    'rel' => 'self',
                    'type' => RestoUtil::$contentTypes['json'],
                    'href' => $this->context->core['baseUrl'] . $this->landingRoot . '/processes'
                )
            )
        );

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
     *          @OA\JsonContent(ref="#/components/schemas/Process")
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
    public function getProcess($params, $showExecutionUnit = false)
    {

        try {

            $results = $this->context->dbDriver->fetch($this->context->dbDriver->pQuery('SELECT ' . $this->processColumns . ' FROM ' . $this->context->dbDriver->commonSchema . '.process WHERE id=($1)', array(
                $params['processId']
            )));

            if (!isset($results) || count($results) !== 1) {
                return RestoLogUtil::httpError(404);
            }

        } catch (Exception $e) {
            return RestoLogUtil::httpError($e->getCode(), $e->getMessage());
        }

        return $this->internalToProcess($results[0], array(
            'showExecutionUnit' => $showExecutionUnit,
            'showIO' => true
        ));

    }

    /**
     * [IMPORTANT] When process is deleted, all related jobs are also deleted from the database
     *
     *  @OA\Delete(
     *      path="/process/{processId}",
     *      summary="Delete a process",
     *      description="Delete a process - this remove all related jobs in cascade",
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
     *          response="200"
     *      ),
     *      @OA\Response(
     *          response="404",
     *          description="Not Found",
     *          @OA\JsonContent(ref="#/components/schemas/NotFoundError")
     *      ),
     *      @OA\Response(
     *          response="410",
     *          description="Gone",
     *          @OA\JsonContent(ref="#/components/schemas/GoneError")
     *      )
     *  )
     *
     *  @param array $params
     */
    public function undeploy($params)
    {

        $process = $this->getProcess($params);

        // Only the owner of the process or the admin can delete it
        if ($process['owner'] !== $this->user->profile['id']) {
            if (!$this->user->hasGroup(RestoConstants::GROUP_ADMIN_ID)) {
                return RestoLogUtil::httpError(403);
            }
        }

        try {

            $results = $this->context->dbDriver->fetch($this->context->dbDriver->pQuery('DELETE FROM ' . $this->context->dbDriver->commonSchema . '.process WHERE id=($1) RETURNING id', array(
                $params['processId']
            )));

            if (!isset($results) || count($results) !== 1) {
                return RestoLogUtil::httpError(500);
            }

        } catch (Exception $e) {
            return RestoLogUtil::httpError($e->getCode(), $e->getMessage());
        }

        return RestoLogUtil::success('Process deleted', array(
            'id' => $results[0]['id']
        ));

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

        $jobs = array();

        try {

            $results = $this->context->dbDriver->query('SELECT ' . $this->jobColumns . ' FROM ' . $this->context->dbDriver->commonSchema . '.job');

            while ($job = pg_fetch_assoc($results)) {
                $jobs[] = $this->internalToJob($job, false);
            }

        } catch (Exception $e) {
            return RestoLogUtil::httpError($e->getCode(), $e->getMessage());
        }

        return array(
            'jobs' => $jobs,
            'links' => array(
                array(
                    'rel' => 'up',
                    'type' => RestoUtil::$contentTypes['json'],
                    'href' => $this->context->core['baseUrl'] . $this->landingRoot
                ),
                array(
                    'rel' => 'self',
                    'type' => RestoUtil::$contentTypes['json'],
                    'href' => $this->context->core['baseUrl'] . $this->landingRoot . '/jobs'
                )
            )
        );

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

        try {

            $results = $this->context->dbDriver->fetch($this->context->dbDriver->pQuery('SELECT ' . $this->jobColumns . ' FROM ' . $this->context->dbDriver->commonSchema . '.job WHERE id=($1)', array(
                $params['jobId']
            )));

            if (!isset($results) || count($results) !== 1) {
                return RestoLogUtil::httpError(404);
            }

        } catch (Exception $e) {
            return RestoLogUtil::httpError($e->getCode(), $e->getMessage());
        }

        return $this->internalToJob($results[0], false);

    }

    /**
     * [IMPORTANT] job is not deleted from database but its status is set as "dismissed"
     *
     *  @OA\Delete(
     *      path="/jobs/{jobId}",
     *      summary="Delete a job",
     *      description="Delete/cancel a job",
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
     *          description="job",
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
     *      ),
     *      @OA\Response(
     *          response="410",
     *          description="Gone",
     *          @OA\JsonContent(ref="#/components/schemas/GoneError")
     *      )
     *  )
     *
     *  @param array $params
     */
    public function deleteJob($params)
    {

        $job = $this->getJob($params);

        // Only the owner of the job or the admin can delete it
        if ($job['owner'] !== $this->user->profile['id']) {
            if (!$this->user->hasGroup(RestoConstants::GROUP_ADMIN_ID)) {
                return RestoLogUtil::httpError(403);
            }
        }

        // The job is already dismissed
        if ($job['status'] === 'dismissed') {
            return RestoLogUtil::httpError(410);
        }

        try {

            $results = $this->context->dbDriver->fetch($this->context->dbDriver->pQuery('UPDATE ' . $this->context->dbDriver->commonSchema . '.job SET status=$2, updated=now(), finished=now() WHERE id=($1) RETURNING ' . $this->jobColumns, array(
                $params['jobId'],
                'dismissed'
            )));

            if (!isset($results) || count($results) !== 1) {
                return RestoLogUtil::httpError(410);
            }

        } catch (Exception $e) {
            return RestoLogUtil::httpError($e->getCode(), $e->getMessage());
        }

        return $this->internalToJob($results[0], false);

    }

    /**
     *  @OA\Post(
     *      path="/processes/{processId}/execution",
     *      summary="Execute a process",
     *      description="Execute a process leading to a job",
     *      tags={"process"},
     *      @OA\Response(
     *          response="200",
     *          description="Job status",
     *          @OA\JsonContent(ref="#/components/schemas/GoneError")
     *      ),
     *      @OA\Response(
     *          response="403",
     *          description="Forbidden",
     *          @OA\JsonContent(ref="#/components/schemas/ForbiddenError")
     *      ),
     *      @OA\RequestBody(
     *          description="Process execution parameters",
     *          required=true,
     *          @OA\JsonContent(
     *              @OA\Property(
     *                  property="inputs",
     *                  type="object",
     *                  @OA\JsonContent()
     *              ),
     *              @OA\Property(
     *                  property="outputs",
     *                  type="object",
     *                  @OA\JsonContent()
     *              ),
     *              @OA\Property(
     *                  property="response",
     *                  type="string",
     *                  enum={"raw", "document"}
     *              )
     *          )
     *      ),
     *      security={
     *          {"basicAuth":{}, "bearerAuth":{}, "queryAuth":{}}
     *      }
     *  )
     * 
     * job TABLE
     * 
     *      "id"                    TEXT PRIMARY KEY,
     *      userid                  BIGINT,
     *      process_id              TEXT REFERENCES __DATABASE_COMMON_SCHEMA__.process (id) ON DELETE CASCADE,
     *      type                    TEXT default 'process',
     *      status                  TEXT,
     *      message                 TEXT,
     *      created                 TIMESTAMP,
     *      started                 TIMESTAMP,
     *      finished                TIMESTAMP,
     *      updated                 TIMESTAMP,
     *      progress                INTEGER
     * 
     * @param array $params
     * @param array $body
     */
    public function executeProcess($params, $body)
    {

        $containerId = $this->launchContainer($this->getProcess($params, true));

        try {
            
            $results = $this->context->dbDriver->fetch($this->context->dbDriver->pQuery('INSERT INTO ' . $this->context->dbDriver->commonSchema . '.job (id, userid, process_id, status, created, started, updated, progress, body, container_id) VALUES ($1, $2, $3, $4, now(), now(), now(), $5, $6, $7) RETURNING ' . $this->jobColumns, array(
                RestoUtil::toUUID(md5(microtime() . rand())),
                $this->user->profile['id'],
                $params['processId'],
                'accepted',
                0,
                json_encode($body, JSON_UNESCAPED_SLASHES),
                $containerId
            )));

            if (count($results) !== 1) {
                throw new Exception(500, 'Cannot execute process ' . $params['processId']);
            }

        } catch (Exception $e) {
            return RestoLogUtil::httpError($e->getCode(), $e->getMessage());
        }

        return $this->internalToJob($results[0], false);

    }

    /**
     * Convert raw process from database to an OGC API Process
     *
     * @param array $params
     * @param boolean $array
     */
    public function getResults($params)
    {
        return array(
            'message' => 'TODO'
        );
    }

    /**
     * Convert raw process from database to an OGC API Process
     *
     * @param array $internalProcess
     * @param array $options
     */
    private function internalToProcess($internalProcess, $options)
    {

        $process = array(
            'id' => $internalProcess['id'],
            'version' => $internalProcess['version'],
            'title' => $internalProcess['title'] ?? null,
            'description' => $internalProcess['description'] ?? null,
            'keywords' => json_decode($internalProcess['keywords'], true)
        );

        $content = json_decode($internalProcess['content'], true);
        foreach ($content as $key => $value) {
            if ( !$options['showIO'] && in_array($key, array('inputs', 'outputs', 'links')) ) {
                continue;
            }
            $process[$key] = $value;
        }

        // ExecutionUnit
        if ( $options['showExecutionUnit'] ) {
            $process['executionUnit'] = json_decode($internalProcess['execution_unit'], true);
        }

        // Links
        $process['links'] = array_merge(
            $content['links'] ?? array(),
            array(
                array(
                    'rel' => 'http://www.opengis.net/def/rel/ogc/1.0/execute',
                    'type' => RestoUtil::$contentTypes['json'],
                    'href' => $this->context->core['baseUrl'] . $this->landingRoot . '/processes/' . $process['id'] . '/execution'
                )
            )
        );

        return $process;

    }

    /**
     * Format input ApplicationPackage to match database requirement
     *
     * @param array $apPackage : process description as json file
     */
    private function apToInternal($apPackage)
    {

        /*
         * Check that executionUnit and processDescription are set
         */
        if ( !is_array($apPackage) || empty($apPackage['processDescription']) || empty($apPackage['executionUnit']) ) {
            RestoLogUtil::httpError(400, 'Invalid input JSON Application Package - must contains both processDescription and executionUnit properties');
        }

        /*
         * Check that processDescription is valid
         */
        if ( !is_array($apPackage['processDescription']['inputs']) || !is_array($apPackage['processDescription']['outputs']) ) {
            RestoLogUtil::httpError(400, 'Invalid input processDescription');
        }

        /*
         * Check that executionUnit is valid
         */
        if ( isset($apPackage['executionUnit']['type']) && $apPackage['executionUnit']['type'] !== 'docker') {
            RestoLogUtil::httpError(400, 'ExecutionUnit supports only type=docker');
        }

        if ( !isset($apPackage['executionUnit']['image']) ) {
            RestoLogUtil::httpError(400, 'ExecutionUnit must provide a non empty image name');
        }
        
        $internalProcess = array(
            // Be permissive here, we create an UUID process id if not set in the inputProcess
            'id' => $apPackage['processDescription']['id'] ?? RestoUtil::toUUID(md5(microtime() . rand())),
            'version' => $apPackage['processDescription']['version'] ?? $this->apiVersion,
            'title' => $apPackage['processDescription']['title'] ?? null,
            'description' => $apPackage['processDescription']['description'] ?? null,
            'keywords' => $apPackage['processDescription']['keywords'] ?? array(),
            'content' => array(),
            'executionUnit' => $apPackage['executionUnit']
        );

        /*
         * Set content values
         */
        foreach (array_values(array('metadata', 'additionalParameters', 'jobControlOptions', 'outputTransmission', 'links', 'inputs', 'outputs')) as $key) {
            if (isset($apPackage['processDescription'][$key])) {
                $internalProcess['content'][$key] = $key === 'links' ? $this->cleanInputLinks($apPackage['processDescription']['links']) : $apPackage['processDescription'][$key];
            }
        }

        return $internalProcess;
    }

    /**
     * Convert raw job from database to an OGC API Job
     *
     * @param array $internalJob
     * @param boolean $showBody -- Set to true only internaly to get the inputs/outputs info and the container_id
     *
     */
    private function internalToJob($internalJob, $showBody)
    {

        $links = array(
            array(
                'rel' => 'status',
                'type' => RestoUtil::$contentTypes['json'],
                'href' => $this->context->core['baseUrl'] . $this->landingRoot . '/jobs/' . $internalJob['id']
            )
        );

        // Status are "accepted", "running", "successful", "failed", "dismissed"
        switch($internalJob['status']) {

            case 'failed':
            case 'successful':
                $links = array_merge($links, array(
                    array(
                        'rel' => $internalJob['status'] === 'successful' ? 'http://www.opengis.net/def/rel/ogc/1.0/results' : 'http://www.opengis.net/def/rel/ogc/1.0/exceptions',
                        'type' => RestoUtil::$contentTypes['json'],
                        'href' => $this->context->core['baseUrl'] . $this->landingRoot . '/jobs/' . $internalJob['id'] . '/results'
                    )
                ));
                break;
        }

        $job = array(
            'jobID' => $internalJob['id'],
            'processId' => $internalJob['process_id'],
            'owner' => $internalJob['owner'],
            'type' => $internalJob['type'],
            'status' => $internalJob['status'],
            'message' => $internalJob['message'],
            'created' => $internalJob['created'],
            'started' => $internalJob['started'],
            'finished' => $internalJob['finished'],
            'updated' => $internalJob['updated'],
            'progress' => (int) $internalJob['progress'],
            'links' => $links
        );

        if ($showBody) {
            $job['body'] = json_decode($internalJob['body'], true);
            $job['container_id'] = $internalJob['container_id'];
        }

        return $job;

    }

    /**
     * Remove input links that are handled by resto
     *
     * List of possible links from specification are defined here https://docs.ogc.org/is/18-062r2/18-062r2.html#toc16
     *
     * [IMPORTANT] Note that the "up" relation is equivalent to the "parent" relation in STAC
     *
     * @param array $links
     * @return array
     */
    private function cleanInputLinks($links)
    {
        $cleanLinks = array();

        $handledByResto = array(
            'self',
            'up',
            'http://www.opengis.net/def/rel/ogc/1.0/conformance',
            'http://www.opengis.net/def/rel/ogc/1.0/exceptions',
            'http://www.opengis.net/def/rel/ogc/1.0/execute',
            'http://www.opengis.net/def/rel/ogc/1.0/job-list',
            'http://www.opengis.net/def/rel/ogc/1.0/processes',
            'http://www.opengis.net/def/rel/ogc/1.0/results'
        );
        for ($i = 0, $ii = count($links); $i < $ii; $i++) {
            $rel = $links[$i]['rel'] ?? null;
            if ($rel && in_array($rel, $handledByResto)) {
                continue;
            }
            $cleanLinks[] = $links[$i];
        }

        return $cleanLinks;
    }

    /**
     * Check if process exists within  database
     *
     * @param string $processId
     * @return boolean
     * @throws Exception
     */
    private function processExists($processId)
    {
        $results = $this->context->dbDriver->fetch($this->context->dbDriver->pQuery('SELECT id FROM ' . $this->context->dbDriver->commonSchema . '.process WHERE id=$1', array($processId)));
        return !empty($results);
    }

    /**
     * Launch container on remote socket
     * 
     * @param $process
     */
    private function launchContainer($process)
    {
        
        // Launch process
        try {
            $remote_socket = $process['executionUnit']['host'] ?? $this->options['executionUnit']['host'] ?? null;
            $processRunner = new ProcessRunner(array(
                'remote_socket' => $remote_socket,
                'ssl' => $process['executionUnit']['ssl'] ?? $this->options['executionUnit']['ssl'] ?? false
            ));
        } catch (Exception $e) {
            return RestoLogUtil::httpError(500, 'Cannot connect to remote_socket ' . $process['executionUnit']['host'] ?? $this->options['executionUnit']['host'] ?? null);
        }
        
        try {    
            $containerId = $processRunner->startContainer($process['executionUnit']['image'], array(), array());
        } catch (Exception $e) {
            return RestoLogUtil::httpError(400, $e->getMessage());
        }

        return $containerId;

    }

}
