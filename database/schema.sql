----------
-- tables
----------
CREATE TABLE "user" (
  id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
  created_at TIMESTAMPTZ NOT NULL DEFAULT NOW()
);

COMMENT ON TABLE "user" IS 'ユーザー';

CREATE TABLE user_detail (
  user_id UUID PRIMARY KEY
    CONSTRAINT fk_user_detail_user
      REFERENCES "user"(id)
      DEFERRABLE INITIALLY DEFERRED,
  name TEXT NOT NULL,
  created_at TIMESTAMPTZ NOT NULL DEFAULT NOW(),
  CONSTRAINT uidx_user_detail_name UNIQUE (name)
);

COMMENT ON TABLE user_detail IS 'ユーザー詳細';
COMMENT ON COLUMN user_detail.name IS 'ユーザー名';

CREATE TABLE user_password_auth (
  user_id UUID PRIMARY KEY
    CONSTRAINT fk_user_password_auth_user
      REFERENCES "user"(id)
      DEFERRABLE INITIALLY DEFERRED,
  password TEXT NOT NULL,
  created_at TIMESTAMPTZ NOT NULL DEFAULT NOW()
);

COMMENT ON TABLE user_password_auth IS 'ユーザーパスワード認証情報';
COMMENT ON COLUMN user_password_auth.password IS 'ユーザーパスワード';

CREATE TABLE issue (
  id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
  created_at TIMESTAMPTZ NOT NULL DEFAULT NOW()
);

COMMENT ON TABLE issue IS 'イシュー';

CREATE TABLE issue_detail_value (
  id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
  issue_id UUID NOT NULL
    CONSTRAINT fk_issue_detail_value_issue
      REFERENCES issue(id)
      DEFERRABLE INITIALLY DEFERRED,
  summary TEXT NOT NULL,
  created_at TIMESTAMPTZ NOT NULL DEFAULT NOW()
);

COMMENT ON TABLE issue_detail_value IS 'イシュー詳細の値';
COMMENT ON COLUMN issue_detail_value.summary IS 'イシューの概要';

CREATE TABLE issue_detail_active (
  issue_id UUID PRIMARY KEY
    CONSTRAINT fk_issue_detail_active_issue
      REFERENCES issue(id)
      DEFERRABLE INITIALLY DEFERRED,
  detail_value_id UUID NOT NULL
    CONSTRAINT fk_issue_detail_active_value
      REFERENCES issue_detail_value(id)
      DEFERRABLE INITIALLY DEFERRED
);

COMMENT ON TABLE issue_detail_active IS 'イシュー詳細の現在値';

CREATE INDEX idx_issue_detail_active_detail_value_id
  ON issue_detail_active (detail_value_id);

CREATE TABLE issue_assignment (
  id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
  issue_id UUID NOT NULL
    CONSTRAINT fk_issue_assignment_issue
      REFERENCES issue(id)
      DEFERRABLE INITIALLY DEFERRED,
  assignee_id UUID NOT NULL
    CONSTRAINT fk_issue_assignment_assignee
      REFERENCES "user"(id)
      DEFERRABLE INITIALLY DEFERRED,
  created_at TIMESTAMPTZ NOT NULL DEFAULT NOW()
);

COMMENT ON TABLE issue_assignment IS 'イシュー担当者割り当て';
COMMENT ON COLUMN issue_assignment.assignee_id IS '担当者のユーザーID';

CREATE INDEX idx_issue_assignment_issue_id
  ON issue_assignment (issue_id, created_at DESC);
CREATE INDEX idx_issue_assignment_assignee_id
  ON issue_assignment (assignee_id);

CREATE TABLE issue_close (
  issue_id UUID PRIMARY KEY
    CONSTRAINT fk_issue_close_issue
      REFERENCES issue(id)
      DEFERRABLE INITIALLY DEFERRED,
  created_at TIMESTAMPTZ NOT NULL DEFAULT NOW()
);

COMMENT ON TABLE issue_close IS 'イシュークローズ';

---------
-- views
---------
CREATE VIEW issue_detail AS
SELECT
  i.id,
  idv.summary,
  idv.created_at AS updated_at,
  i.created_at,
  ic.created_at AS closed_at
FROM
  issue i
  INNER JOIN issue_detail_active ida
    ON ida.issue_id = i.id
  INNER JOIN issue_detail_value idv
    ON idv.id = ida.detail_value_id
  LEFT JOIN issue_close ic
    ON ic.issue_id = i.id
;

COMMENT ON VIEW issue_detail IS 'イシュー詳細';
COMMENT ON COLUMN issue_detail.summary IS 'イシューの概要';
COMMENT ON COLUMN issue_detail.updated_at IS '最終更新日時';
COMMENT ON COLUMN issue_detail.created_at IS 'イシュー作成日時';
COMMENT ON COLUMN issue_detail.closed_at IS 'イシュークローズ日時';

CREATE VIEW issue_assignees AS
SELECT
  i.id AS issue_id,
  ia_latest.assignee_id,
  ia_latest.created_at AS assigned_at
FROM
  issue i
  LEFT JOIN LATERAL (
    SELECT
      assignee_id,
      created_at
    FROM
      issue_assignment ia
    WHERE
      ia.issue_id = i.id
    ORDER BY
      ia.created_at DESC
    LIMIT 1
  ) ia_latest ON TRUE
;

COMMENT ON VIEW issue_assignees IS 'イシュー担当者';
COMMENT ON COLUMN issue_assignees.assignee_id IS '担当者のユーザーID';
COMMENT ON COLUMN issue_assignees.assigned_at IS '担当者割り当て日時';
