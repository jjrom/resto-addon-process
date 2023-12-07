
--
-- Process
--
CREATE TABLE IF NOT EXISTS __DATABASE_COMMON_SCHEMA__.process (
    -- Unique identifier provided during creation
    "id"                    TEXT PRIMARY KEY,
    -- Owner reference resto.user
    userid                  BIGINT,
    title                   TEXT,
    description             TEXT,
    version                 TEXT,
    keywords                TEXT[],
    created                 TIMESTAMP,
    content                 JSON,             -- Every properties from OGC API Processes process object
    execution_unit          JSON
);

--
-- Job
--
CREATE TABLE IF NOT EXISTS __DATABASE_COMMON_SCHEMA__.job (
    -- Unique identifier provided during creation
    "id"                    TEXT PRIMARY KEY,
    -- Owner reference resto.user
    userid                  BIGINT,
    -- Reference process id
    process_id              TEXT REFERENCES __DATABASE_COMMON_SCHEMA__.process (id) ON DELETE CASCADE,
    type                    TEXT default 'process',
    status                  TEXT,
    message                 TEXT,
    created                 TIMESTAMP,
    started                 TIMESTAMP,
    finished                TIMESTAMP,
    updated                 TIMESTAMP,
    progress                INTEGER,
    body                    JSON -- This is what is post for process execution i.e. inputs and outputs
);

--
-- Indexes
--
CREATE INDEX IF NOT EXISTS idx_status_job ON __DATABASE_COMMON_SCHEMA__.job (status);
CREATE INDEX IF NOT EXISTS idx_userid_job ON __DATABASE_COMMON_SCHEMA__.job (userid);


