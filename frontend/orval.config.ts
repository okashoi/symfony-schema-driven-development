import { defineConfig } from 'orval';

export default defineConfig({
  myIssueTracker: {
    input: {
      target: '../api-schema/api-schema.yaml',
    },
    output: {
      mode: 'split',
      client: 'react-query',
      target: './lib/api-client/generated/endpoints',
      schemas: './lib/api-client/generated/models',
      override: {
        mutator: {
          path: './lib/api-client/customFetch.ts',
          name: 'customFetch',
        },
      },
    },
  },
  myIssueTrackerZod: {
    input: {
      target: '../api-schema/api-schema.yaml',
    },
    output: {
      mode: 'split',
      client: 'zod',
      target: './lib/api-client/generated/endpoints',
      fileExtension: '.zod.ts',
      override: {
        zod: {
          generateEachHttpStatus: true,
          generate: {
            response: true,
            query: true,
            param: true,
            header: true,
            body: true,
          },
          strict: {
            response: true,
            query: true,
            param: true,
            header: true,
            body: true,
          },
          dateTimeOptions: {
            offset: true,
          },
        },
      },
    },
  },
});
