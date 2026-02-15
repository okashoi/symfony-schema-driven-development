export class ApiRequestClientError extends Error {
  /**
   * HTTPステータスコード
   */
  status: number;

  /**
   * レスポンスボディ
   */
  content: unknown;

  constructor(status: number, content: unknown) {
    super(`API request failed with status ${status}`);
    this.status = status;
    this.content = content;
  }
}
