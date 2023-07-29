import { CodegenConfig } from '@graphql-codegen/cli';

const config: CodegenConfig = {
  schema: 'http://sylar-webserver/graphql/',
  documents: ['assets/graphQL/**/*.ts*'],
  ignoreNoDocuments: true,
  overwrite: true,
  generates: {
    './assets/gql/': {
      preset: 'client',
      plugins: [{ add: { content: '// @ts-nocheck' } }, 'typescript'],
      config: {
        maybeValue: 'T'
      }
    }
  },
  hooks: {
    afterOneFileWrite: [
      'prettier --write',
      'eslint --ext=.jsx,.js,.tsx,.ts --fix'
    ]
  }
};

export default config;
