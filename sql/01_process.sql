
--
-- Process
--
CREATE TABLE IF NOT EXISTS __DATABASE_COMMON_SCHEMA__.process (
    -- Unique identifier provided during creation
    "id"                    TEXT PRIMARY KEY,
    -- Owner reference resto.user
    userid                  BIGINT,
    title                   TEXT
    description             TEXT,
    version                 TEXT,
    keywords                TEXT[],
    metadata                JSON,
    created                 TIMESTAMP,
    jobControlOptions       TEXT[],
    outputTransmission      TEXT[],
    additionalParameters    JSON,
    links                   JSON,    
    inputs                  JSON,
    outputs                 JSON
);

--
-- Job
--
CREATE TABLE IF NOT EXISTS __DATABASE_COMMON_SCHEMA__.job (
    -- Unique identifier provided during creation
    "id"                TEXT PRIMARY KEY,
    -- Owner reference resto.user
    userid              BIGINT,
    -- Reference process id
    process_id          TEXT,
    type                TEXT default 'process',
    status              INT DEFAULT 202,
    message             TEXT,
    created             TIMESTAMP,
    started             TIMESTAMP,
    finished            TIMESTAMP,
    updated             TIMESTAMP,
    progress            INTEGER,
    response            TEXT default 'raw',
    subscriber          JSON,
    links               JSON,    
    inputs              JSON,
    outputs             JSON

);

--
-- Indexes
--
CREATE INDEX IF NOT EXISTS idx_status_job ON __DATABASE_COMMON_SCHEMA__.job (status);



