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
 *          property="additionalParameters",
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
 *              ),
 *              @OA\Property(
 *                  property="parameters",
 *                  type="array",
 *                  @OA\Items(
 *                      type="object",
 *                      required={"name", "value"},
 *                      @OA\Property(
 *                          property="name",
 *                          type="string"
 *                      ),
 *                      @OA\Property(
 *                          property="name",
 *                          oneOf={"integer", "number", "string", "object"}
 *                      )
 *                  )
 *              ),
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

    private $landingRoot = '/oapi-p';

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
     *         @OA\JsonContent(ref="#/components/schemas/Process")
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
    public function addProcess($params, $body)
    {

        $internalProcess = $this->processToInternal($body);

        try {

            $results = $this->context->dbDriver->fetch($this->context->dbDriver->pQuery('INSERT INTO ' . $this->context->dbDriver->commonSchema . '.process (id, userid, title, description, version, keywords, content, created) VALUES ($1,$2, $3, $4, $5, $6, $7, now()) ON CONFLICT (id) DO NOTHING RETURNING id', array(
                $internalProcess['id'],
                $this->user->profile['id'],
                $internalProcess['title'],
                $internalProcess['description'],
                $internalProcess['version'],
                isset($internalProcess['keywords']) ? '{' . join(',', $internalProcess['keywords']) . '}' : null,
                json_encode($internalProcess['content'], JSON_UNESCAPED_SLASHES)
            )));
            if (count($results) !== 1) {
                throw new Exception(409, 'Process already exist ' . $internalProcess['id']);
            }

        } catch (Exception $e) {
            return RestoLogUtil::httpError($e->getCode(), $e->getMessage());
        }

        return RestoLogUtil::success('Process created', array(
            'id' => $results[0]['id']
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

            $results = $this->context->dbDriver->query('SELECT id, userid as owner, title, description, version, keywords, to_iso8601(created) as created, content FROM ' . $this->context->dbDriver->commonSchema . '.process');
            
            while ($process = pg_fetch_assoc($results)) {
                $processes[] = $this->internalToProcess($process, false);
            }
        
        } catch (Exception $e) {
            return RestoLogUtil::httpError($e->getCode(), $e->getMessage());
        }

        return array(
            'processes' => $processes,
            'links' => array()
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
    public function getProcess($params)
    {
        
        try {

            $results = $this->context->dbDriver->fetch($this->context->dbDriver->pQuery('SELECT id, userid as owner, title, description, version, keywords, to_iso8601(created) as created, content FROM ' . $this->context->dbDriver->commonSchema . '.process WHERE id=($1)', array(
                $params['processId']
            )));
    
            if ( !isset($results) || count($results) !== 1 ) {
                return RestoLogUtil::httpError(404);
            }
            
        } catch (Exception $e) {
            return RestoLogUtil::httpError($e->getCode(), $e->getMessage());
        }

        return $this->internalToProcess($results[0], true);

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


    public function deleteJob($params)
    {

    }

    public function executeProcess($params, $body)
    {

    }

    /**
     * Convert raw process from database to an OGC API Process
     *
     * @param array $internalProcess
     * @param boolean $fullDescription
     */
    private function internalToProcess($internalProcess, $fullDescription)
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
            if ( !$fullDescription && in_array($key, array('inputs', 'outputs')) ) {
                continue;
            }
            $process[$key] = $key === 'links' ? $this->setOutputLinks($value) : $value;
        }

        return $process;
    }

    /**
     * Format input process to match database requirement
     *
     * @param array $inputProcess : process description as json file
     */
    private function processToInternal($inputProcess)
    {

        /*
         * Check that inputProcess is a valid array
         */
        if (!is_array($inputProcess)) {
            RestoLogUtil::httpError(400, 'Invalid input JSON process');
        }

        $internalProcess = array(
            // Be permissive here, we create an UUID process id if not set in the inputProcess
            'id' => $inputProcess['id'] ?? RestoUtil::toUUID(md5(microtime().rand())),
            'version' => $inputProcess['version'] ?? $this->apiVersion,
            'title' => $inputProcess['title'] ?? null,
            'description' => $inputProcess['description'] ?? null,
            'keywords' => $inputProcess['keywords'] ?? array(),
            'content' => array()
        );

        /*
         * Set content values
         */
        foreach (array_values(array('metadata', 'additionalParameters', 'jobControlOptions', 'outputTransmission', 'links', 'inputs', 'outputs')) as $key) {
            if ( isset($inputProcess[$key]) ) {
                $internalProcess['content'][$key] = $key === 'links' ? $this->cleanInputLinks($inputProcess['links']) : $inputProcess[$key];
            }
        }

        return $internalProcess;
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
     * Add resto links
     * 
     * @param array $links
     * @return array
     */
    private function setOutputLinks($links)
    {
        return $links;
    }

}
