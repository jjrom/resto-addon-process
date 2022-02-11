
--
-- Entities
--
CREATE TABLE IF NOT EXISTS resto.process (

    -- Unique identifier provided during creation
    "id"                TEXT PRIMARY KEY,

    -- Process status as HTTP Status
    status              INT,

    -- Process title
    title                TEXT,

    -- Process description
    description         TEXT,

    -- Reference resto.user
    userid              BIGINT,

    -- Timestamp of process creation
    created             TIMESTAMP,

    -- Timestamp of process ending
    finished            TIMESTAMP,

    -- Process input - can contains any property
    input               JSON,

    -- Process output
    output              JSON

);

--
-- Indexes
--
CREATE INDEX IF NOT EXISTS idx_status_process ON resto.process (status);



