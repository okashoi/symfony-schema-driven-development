# Symfony + NelmioApiDocBundle を使ったスキーマ駆動開発

本リポジトリは PHPerKaigi 2026 のセッション [Symfony + NelmioApiDocBundle を使ったスキーマ駆動開発](https://fortee.jp/phperkaigi-2026/proposal/a54af6b2-679a-4d8b-8f47-427a4a182622)の参考実装です。

題材は課題管理（イシュートラッカー）アプリケーションです。

## 技術スタック

### バックエンド

- PHP 8.5 / Symfony 8.0
- Doctrine ORM
- NelmioApiDocBundle（OpenAPI スキーマ生成）

### フロントエンド

- React 19 / Next.js 16
- TypeScript 5
- Tailwind CSS 4
- TanStack Query / React Hook Form / Zod
- openapi-fetch / openapi-typescript（スキーマからの型・クライアント自動生成）

### データベース

- PostgreSQL 18

## 環境構築手順

### 前提条件

- Docker および Docker Compose がインストールされていること
- [task](https://taskfile.dev/) がインストールされていること

### セットアップ


```bash
cp .env.example .env
task install
```

セットアップ完了後、以下の URL でアクセスできます。

| サービス | URL |
|----------|-----|
| フロントエンド | `http://${LOCAL_IP}:${FRONTEND_PORT}` |
| バックエンド API | `http://${LOCAL_IP}:${BACKEND_PORT}` |
| API ドキュメント | `http://${LOCAL_IP}:${BACKEND_PORT}/api/doc` |

## 開発用コマンド

### Docker 操作

| コマンド | 説明 |
|----------|------|
| `task up` | コンテナ起動 |
| `task down` | コンテナ停止 |
| `task restart` | コンテナ再起動 |
| `task logs -- <service>` | コンテナログ表示 |
| `task clean` | コンテナ・ボリュームの完全削除 |

### バックエンド

| コマンド | 説明 |
|----------|------|
| `task composer -- <args>` | Composer コマンド実行 |
| `task backend:console -- <args>` | Symfony Console コマンド実行 |
| `task backend:shell` | バックエンドコンテナへのシェルアクセス |

### フロントエンド

| コマンド | 説明 |
|----------|------|
| `task frontend:npm -- <args>` | npm コマンド実行 |
| `task frontend:shell` | フロントエンドコンテナへのシェルアクセス |

### API スキーマ

| コマンド | 説明 |
|----------|------|
| `task api-schema:dump` | OpenAPI スキーマ YAML を生成 |
| `task api-schema:generate-client` | スキーマから TypeScript API クライアントを生成 |
