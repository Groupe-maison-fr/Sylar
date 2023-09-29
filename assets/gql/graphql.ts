/* eslint-disable */
// @ts-nocheck
import { TypedDocumentNode as DocumentNode } from '@graphql-typed-document-node/core';
export type Maybe<T> = T | null;
export type InputMaybe<T> = Maybe<T>;
export type Exact<T extends { [key: string]: unknown }> = {
  [K in keyof T]: T[K];
};
export type MakeOptional<T, K extends keyof T> = Omit<T, K> & {
  [SubKey in K]?: Maybe<T[SubKey]>;
};
export type MakeMaybe<T, K extends keyof T> = Omit<T, K> & {
  [SubKey in K]: Maybe<T[SubKey]>;
};
export type MakeEmpty<
  T extends { [key: string]: unknown },
  K extends keyof T,
> = { [_ in K]?: never };
export type Incremental<T> =
  | T
  | {
      [P in keyof T]?: P extends ' $fragmentName' | '__typename' ? T[P] : never;
    };
/** All built-in and custom scalars, mapped to their actual values */
export type Scalars = {
  ID: { input: string; output: string };
  String: { input: string; output: string };
  Boolean: { input: boolean; output: boolean };
  Int: { input: number; output: number };
  Float: { input: number; output: number };
  DateTime: { input: any; output: any };
};

export type AddReservationInput = {
  index?: InputMaybe<Scalars['Int']['input']>;
  name: Scalars['String']['input'];
  service: Scalars['String']['input'];
};

export type AddReservationOutput = FailedOutput | SuccessOutput;

export type Command = {
  __typename?: 'Command';
  name: Scalars['String']['output'];
  output: Array<CommandExecutorResult>;
  subCommands: Array<Scalars['String']['output']>;
};

export type CommandExecutorResult = {
  __typename?: 'CommandExecutorResult';
  output: Array<Scalars['String']['output']>;
  subCommand: Scalars['String']['output'];
};

export type Container = {
  __typename?: 'Container';
  containerName: Scalars['String']['output'];
  dockerState?: Maybe<Scalars['String']['output']>;
  exposedPorts: Array<Scalars['Int']['output']>;
  instanceIndex: Scalars['Int']['output'];
  instanceName: Scalars['String']['output'];
  isMaster: Scalars['Boolean']['output'];
  masterName: Scalars['String']['output'];
  time: Scalars['Int']['output'];
  uptime: Scalars['Int']['output'];
  zfsFilesystem?: Maybe<Filesystem>;
  zfsFilesystemName: Scalars['String']['output'];
};

export type DebugTraceCall = {
  __typename?: 'DebugTraceCall';
  arguments: Array<DebugTraceCallArgument>;
  class?: Maybe<Scalars['String']['output']>;
  file?: Maybe<Scalars['String']['output']>;
  function?: Maybe<Scalars['String']['output']>;
  line?: Maybe<Scalars['Int']['output']>;
  namespace?: Maybe<Scalars['String']['output']>;
  short_class?: Maybe<Scalars['String']['output']>;
  type?: Maybe<Scalars['String']['output']>;
};

export type DebugTraceCallArgument = {
  __typename?: 'DebugTraceCallArgument';
  type?: Maybe<Scalars['String']['output']>;
  value?: Maybe<Scalars['String']['output']>;
};

export type DeleteReservationInput = {
  index?: InputMaybe<Scalars['Int']['input']>;
  name: Scalars['String']['input'];
  service: Scalars['String']['input'];
};

export type DeleteReservationOutput = FailedOutput | SuccessOutput;

export type Environment = {
  __typename?: 'Environment';
  name: Scalars['String']['output'];
  value: Scalars['String']['output'];
};

export type ErrorInterface = {
  code: Scalars['String']['output'];
  message: Scalars['String']['output'];
};

export type FailedMessage = {
  __typename?: 'FailedMessage';
  backtrace: Array<DebugTraceCall>;
  className: Scalars['String']['output'];
  date?: Maybe<Scalars['DateTime']['output']>;
  exceptionMessage?: Maybe<Scalars['String']['output']>;
  flattenException?: Maybe<FlattenException>;
  id: Scalars['ID']['output'];
  message: Scalars['String']['output'];
};

export type FailedMessageOutput = {
  __typename?: 'FailedMessageOutput';
  success?: Maybe<Scalars['Boolean']['output']>;
};

export type FailedOutput = ErrorInterface & {
  __typename?: 'FailedOutput';
  code: Scalars['String']['output'];
  message: Scalars['String']['output'];
};

export type Filesystem = {
  __typename?: 'Filesystem';
  available: Scalars['Float']['output'];
  creationTimestamp: Scalars['Int']['output'];
  mountPoint: Scalars['String']['output'];
  name: Scalars['String']['output'];
  origin: Scalars['String']['output'];
  refer: Scalars['Float']['output'];
  type: Scalars['String']['output'];
  used: Scalars['Float']['output'];
  usedByChild: Scalars['Float']['output'];
  usedByDataset: Scalars['Float']['output'];
  usedByRefreservation: Scalars['Float']['output'];
  usedBySnapshot: Scalars['Float']['output'];
};

export type FlattenException = {
  __typename?: 'FlattenException';
  asString?: Maybe<Scalars['String']['output']>;
  class?: Maybe<Scalars['String']['output']>;
  code?: Maybe<Scalars['Int']['output']>;
  file?: Maybe<Scalars['String']['output']>;
  headers: Array<Scalars['String']['output']>;
  line?: Maybe<Scalars['String']['output']>;
  message?: Maybe<Scalars['String']['output']>;
  previous?: Maybe<FlattenException>;
  statusCode?: Maybe<Scalars['Int']['output']>;
  statusText?: Maybe<Scalars['String']['output']>;
  traceAsString?: Maybe<Scalars['String']['output']>;
};

export type ForceDestroyContainerInput = {
  name: Scalars['String']['input'];
};

export type ForceDestroyContainerOutput = FailedOutput | SuccessOutput;

export type ForceDestroyFilesystemInput = {
  name: Scalars['String']['input'];
};

export type ForceDestroyFilesystemOutput = FailedOutput | SuccessOutput;

export type Label = {
  __typename?: 'Label';
  name: Scalars['String']['output'];
  value: Scalars['String']['output'];
};

export type Mutations = {
  __typename?: 'Mutations';
  addReservation: AddReservationOutput;
  deleteReservation: DeleteReservationOutput;
  forceDestroyContainer: ForceDestroyContainerOutput;
  forceDestroyFilesystem: ForceDestroyFilesystemOutput;
  rejectFailedMessage: FailedMessageOutput;
  restartService: RestartServiceOutput;
  retryFailedMessage: FailedMessageOutput;
  startService: StartServiceOutput;
  stopService: StopServiceOutput;
};

export type MutationsAddReservationArgs = {
  input: AddReservationInput;
};

export type MutationsDeleteReservationArgs = {
  input: DeleteReservationInput;
};

export type MutationsForceDestroyContainerArgs = {
  input: ForceDestroyContainerInput;
};

export type MutationsForceDestroyFilesystemArgs = {
  input: ForceDestroyFilesystemInput;
};

export type MutationsRejectFailedMessageArgs = {
  input: RejectFailedMessageInput;
};

export type MutationsRestartServiceArgs = {
  input: RestartServiceInput;
};

export type MutationsRetryFailedMessageArgs = {
  input: RetryFailedMessageInput;
};

export type MutationsStartServiceArgs = {
  input: StartServiceInput;
};

export type MutationsStopServiceArgs = {
  input: StopServiceInput;
};

export type Port = {
  __typename?: 'Port';
  containerPort?: Maybe<Scalars['String']['output']>;
  hostIp?: Maybe<Scalars['String']['output']>;
  hostPort?: Maybe<Scalars['String']['output']>;
};

export type Query = {
  __typename?: 'Query';
  commandByName?: Maybe<Command>;
  commands: Array<Command>;
  containers: Array<Container>;
  failedMessage: FailedMessage;
  failedMessages: Array<FailedMessage>;
  filesystems: Array<Filesystem>;
  reservations: Array<Reservation>;
  services: Array<Service>;
};

export type QueryCommandByNameArgs = {
  commandName: Scalars['String']['input'];
};

export type QueryFailedMessageArgs = {
  id?: InputMaybe<Scalars['Int']['input']>;
};

export type QueryFailedMessagesArgs = {
  max?: InputMaybe<Scalars['Int']['input']>;
};

export type RejectFailedMessageInput = {
  ids?: InputMaybe<Array<Scalars['ID']['input']>>;
};

export type Reservation = {
  __typename?: 'Reservation';
  index: Scalars['Int']['output'];
  name: Scalars['String']['output'];
  service: Scalars['String']['output'];
};

export type RestartServiceInput = {
  index?: InputMaybe<Scalars['Int']['input']>;
  instanceName: Scalars['String']['input'];
  masterName: Scalars['String']['input'];
};

export type RestartServiceOutput = FailedOutput | SuccessOutput;

export type RetryFailedMessageInput = {
  id: Scalars['ID']['input'];
};

export type Service = {
  __typename?: 'Service';
  command?: Maybe<Scalars['String']['output']>;
  containers: Array<Container>;
  environments: Array<Environment>;
  image: Scalars['String']['output'];
  labels: Array<Label>;
  name: Scalars['String']['output'];
  ports: Array<Port>;
};

export type StartServiceInput = {
  index?: InputMaybe<Scalars['Int']['input']>;
  instanceName: Scalars['String']['input'];
  masterName: Scalars['String']['input'];
};

export type StartServiceOutput = FailedOutput | SuccessOutput;

export type StopServiceInput = {
  instanceName: Scalars['String']['input'];
  masterName: Scalars['String']['input'];
};

export type StopServiceOutput = FailedOutput | SuccessOutput;

export type SuccessOutput = {
  __typename?: 'SuccessOutput';
  message?: Maybe<Scalars['String']['output']>;
  success: Scalars['Boolean']['output'];
};

export type MutationForceDestroyContainerMutationVariables = Exact<{
  name: Scalars['String']['input'];
}>;

export type MutationForceDestroyContainerMutation = {
  __typename?: 'Mutations';
  forceDestroyContainer:
    | { __typename?: 'FailedOutput'; code: string }
    | { __typename?: 'SuccessOutput'; success: boolean };
};

export type MutationForceDestroyFilesystemMutationVariables = Exact<{
  name: Scalars['String']['input'];
}>;

export type MutationForceDestroyFilesystemMutation = {
  __typename?: 'Mutations';
  forceDestroyFilesystem:
    | { __typename?: 'FailedOutput'; code: string; message: string }
    | { __typename?: 'SuccessOutput'; success: boolean };
};

export type MutationRejectFailedMessageMutationVariables = Exact<{
  ids?: InputMaybe<Array<Scalars['ID']['input']> | Scalars['ID']['input']>;
}>;

export type MutationRejectFailedMessageMutation = {
  __typename?: 'Mutations';
  rejectFailedMessage: {
    __typename?: 'FailedMessageOutput';
    success?: boolean | null;
  };
};

export type MutationRetryFailedMessageMutationVariables = Exact<{
  id: Scalars['ID']['input'];
}>;

export type MutationRetryFailedMessageMutation = {
  __typename?: 'Mutations';
  retryFailedMessage: {
    __typename?: 'FailedMessageOutput';
    success?: boolean | null;
  };
};

export type FailedMessageQueryVariables = Exact<{
  id: Scalars['Int']['input'];
}>;

export type FailedMessageQuery = {
  __typename?: 'Query';
  failedMessage: {
    __typename?: 'FailedMessage';
    id: string;
    className: string;
    message: string;
    exceptionMessage?: string | null;
    date?: any | null;
    backtrace: Array<{
      __typename?: 'DebugTraceCall';
      namespace?: string | null;
      short_class?: string | null;
      class?: string | null;
      type?: string | null;
      function?: string | null;
      file?: string | null;
      line?: number | null;
      arguments: Array<{
        __typename?: 'DebugTraceCallArgument';
        type?: string | null;
        value?: string | null;
      }>;
    }>;
    flattenException?: {
      __typename?: 'FlattenException';
      message?: string | null;
      code?: number | null;
      traceAsString?: string | null;
      class?: string | null;
      statusCode?: number | null;
      statusText?: string | null;
      headers: Array<string>;
      file?: string | null;
      line?: string | null;
      previous?: {
        __typename?: 'FlattenException';
        message?: string | null;
        code?: number | null;
        class?: string | null;
        statusCode?: number | null;
        statusText?: string | null;
        headers: Array<string>;
        file?: string | null;
        line?: string | null;
      } | null;
    } | null;
  };
};

export type FailedMessagesQueryVariables = Exact<{
  max: Scalars['Int']['input'];
}>;

export type FailedMessagesQuery = {
  __typename?: 'Query';
  failedMessages: Array<{
    __typename?: 'FailedMessage';
    id: string;
    className: string;
    exceptionMessage?: string | null;
    date?: any | null;
  }>;
};

export type MutationAddReservationMutationVariables = Exact<{
  service: Scalars['String']['input'];
  index: Scalars['Int']['input'];
  name: Scalars['String']['input'];
}>;

export type MutationAddReservationMutation = {
  __typename?: 'Mutations';
  addReservation:
    | { __typename?: 'FailedOutput'; code: string; message: string }
    | { __typename?: 'SuccessOutput'; success: boolean };
};

export type MutationDeleteReservationMutationVariables = Exact<{
  service: Scalars['String']['input'];
  name: Scalars['String']['input'];
  index: Scalars['Int']['input'];
}>;

export type MutationDeleteReservationMutation = {
  __typename?: 'Mutations';
  deleteReservation:
    | { __typename?: 'FailedOutput'; code: string; message: string }
    | { __typename?: 'SuccessOutput'; success: boolean };
};

export type ReservationsQueryVariables = Exact<{ [key: string]: never }>;

export type ReservationsQuery = {
  __typename?: 'Query';
  reservations: Array<{
    __typename?: 'Reservation';
    service: string;
    name: string;
    index: number;
  }>;
};

export type MutationRestartServiceMutationVariables = Exact<{
  masterName: Scalars['String']['input'];
  instanceName: Scalars['String']['input'];
}>;

export type MutationRestartServiceMutation = {
  __typename?: 'Mutations';
  restartService:
    | { __typename?: 'FailedOutput'; code: string; message: string }
    | { __typename?: 'SuccessOutput'; success: boolean };
};

export type MutationStartServiceMutationVariables = Exact<{
  masterName: Scalars['String']['input'];
  index?: InputMaybe<Scalars['Int']['input']>;
  instanceName: Scalars['String']['input'];
}>;

export type MutationStartServiceMutation = {
  __typename?: 'Mutations';
  startService:
    | { __typename?: 'FailedOutput'; code: string; message: string }
    | { __typename?: 'SuccessOutput'; success: boolean };
};

export type MutationStopServiceMutationVariables = Exact<{
  masterName: Scalars['String']['input'];
  instanceName: Scalars['String']['input'];
}>;

export type MutationStopServiceMutation = {
  __typename?: 'Mutations';
  stopService:
    | { __typename?: 'FailedOutput'; code: string; message: string }
    | { __typename?: 'SuccessOutput'; success: boolean };
};

export type ContainersQueryVariables = Exact<{ [key: string]: never }>;

export type ContainersQuery = {
  __typename?: 'Query';
  containers: Array<{
    __typename?: 'Container';
    containerName: string;
    masterName: string;
    instanceName: string;
    instanceIndex: number;
    zfsFilesystemName: string;
    time: number;
    uptime: number;
    dockerState?: string | null;
  }>;
};

export type FilesystemsQueryVariables = Exact<{ [key: string]: never }>;

export type FilesystemsQuery = {
  __typename?: 'Query';
  filesystems: Array<{
    __typename?: 'Filesystem';
    name: string;
    type: string;
    origin: string;
    mountPoint: string;
    available: number;
    refer: number;
    used: number;
    usedByChild: number;
    usedByDataset: number;
    usedByRefreservation: number;
    usedBySnapshot: number;
    creationTimestamp: number;
  }>;
};

export type ServicesAndInstancesQueryVariables = Exact<{
  [key: string]: never;
}>;

export type ServicesAndInstancesQuery = {
  __typename?: 'Query';
  services: Array<{
    __typename?: 'Service';
    name: string;
    containers: Array<{
      __typename?: 'Container';
      containerName: string;
      instanceName: string;
      instanceIndex: number;
    }>;
  }>;
};

export type ServicesQueryVariables = Exact<{ [key: string]: never }>;

export type ServicesQuery = {
  __typename?: 'Query';
  services: Array<{
    __typename?: 'Service';
    name: string;
    image: string;
    command?: string | null;
    labels: Array<{ __typename?: 'Label'; name: string; value: string }>;
    environments: Array<{
      __typename?: 'Environment';
      name: string;
      value: string;
    }>;
    ports: Array<{
      __typename?: 'Port';
      containerPort?: string | null;
      hostPort?: string | null;
      hostIp?: string | null;
    }>;
    containers: Array<{
      __typename?: 'Container';
      containerName: string;
      masterName: string;
      instanceName: string;
      instanceIndex: number;
      zfsFilesystemName: string;
      exposedPorts: Array<number>;
      time: number;
      uptime: number;
      dockerState?: string | null;
      zfsFilesystem?: {
        __typename?: 'Filesystem';
        name: string;
        type: string;
        origin: string;
        mountPoint: string;
        available: number;
        used: number;
        usedByChild: number;
        usedByDataset: number;
        usedByRefreservation: number;
        usedBySnapshot: number;
        creationTimestamp: number;
      } | null;
    }>;
  }>;
};

export const MutationForceDestroyContainerDocument = {
  kind: 'Document',
  definitions: [
    {
      kind: 'OperationDefinition',
      operation: 'mutation',
      name: { kind: 'Name', value: 'MutationForceDestroyContainer' },
      variableDefinitions: [
        {
          kind: 'VariableDefinition',
          variable: { kind: 'Variable', name: { kind: 'Name', value: 'name' } },
          type: {
            kind: 'NonNullType',
            type: {
              kind: 'NamedType',
              name: { kind: 'Name', value: 'String' },
            },
          },
        },
      ],
      selectionSet: {
        kind: 'SelectionSet',
        selections: [
          {
            kind: 'Field',
            name: { kind: 'Name', value: 'forceDestroyContainer' },
            arguments: [
              {
                kind: 'Argument',
                name: { kind: 'Name', value: 'input' },
                value: {
                  kind: 'ObjectValue',
                  fields: [
                    {
                      kind: 'ObjectField',
                      name: { kind: 'Name', value: 'name' },
                      value: {
                        kind: 'Variable',
                        name: { kind: 'Name', value: 'name' },
                      },
                    },
                  ],
                },
              },
            ],
            selectionSet: {
              kind: 'SelectionSet',
              selections: [
                {
                  kind: 'InlineFragment',
                  typeCondition: {
                    kind: 'NamedType',
                    name: { kind: 'Name', value: 'SuccessOutput' },
                  },
                  selectionSet: {
                    kind: 'SelectionSet',
                    selections: [
                      {
                        kind: 'Field',
                        name: { kind: 'Name', value: 'success' },
                      },
                    ],
                  },
                },
                {
                  kind: 'InlineFragment',
                  typeCondition: {
                    kind: 'NamedType',
                    name: { kind: 'Name', value: 'FailedOutput' },
                  },
                  selectionSet: {
                    kind: 'SelectionSet',
                    selections: [
                      { kind: 'Field', name: { kind: 'Name', value: 'code' } },
                    ],
                  },
                },
              ],
            },
          },
        ],
      },
    },
  ],
} as unknown as DocumentNode<
  MutationForceDestroyContainerMutation,
  MutationForceDestroyContainerMutationVariables
>;
export const MutationForceDestroyFilesystemDocument = {
  kind: 'Document',
  definitions: [
    {
      kind: 'OperationDefinition',
      operation: 'mutation',
      name: { kind: 'Name', value: 'MutationForceDestroyFilesystem' },
      variableDefinitions: [
        {
          kind: 'VariableDefinition',
          variable: { kind: 'Variable', name: { kind: 'Name', value: 'name' } },
          type: {
            kind: 'NonNullType',
            type: {
              kind: 'NamedType',
              name: { kind: 'Name', value: 'String' },
            },
          },
        },
      ],
      selectionSet: {
        kind: 'SelectionSet',
        selections: [
          {
            kind: 'Field',
            name: { kind: 'Name', value: 'forceDestroyFilesystem' },
            arguments: [
              {
                kind: 'Argument',
                name: { kind: 'Name', value: 'input' },
                value: {
                  kind: 'ObjectValue',
                  fields: [
                    {
                      kind: 'ObjectField',
                      name: { kind: 'Name', value: 'name' },
                      value: {
                        kind: 'Variable',
                        name: { kind: 'Name', value: 'name' },
                      },
                    },
                  ],
                },
              },
            ],
            selectionSet: {
              kind: 'SelectionSet',
              selections: [
                {
                  kind: 'InlineFragment',
                  typeCondition: {
                    kind: 'NamedType',
                    name: { kind: 'Name', value: 'SuccessOutput' },
                  },
                  selectionSet: {
                    kind: 'SelectionSet',
                    selections: [
                      {
                        kind: 'Field',
                        name: { kind: 'Name', value: 'success' },
                      },
                    ],
                  },
                },
                {
                  kind: 'InlineFragment',
                  typeCondition: {
                    kind: 'NamedType',
                    name: { kind: 'Name', value: 'FailedOutput' },
                  },
                  selectionSet: {
                    kind: 'SelectionSet',
                    selections: [
                      { kind: 'Field', name: { kind: 'Name', value: 'code' } },
                      {
                        kind: 'Field',
                        name: { kind: 'Name', value: 'message' },
                      },
                    ],
                  },
                },
              ],
            },
          },
        ],
      },
    },
  ],
} as unknown as DocumentNode<
  MutationForceDestroyFilesystemMutation,
  MutationForceDestroyFilesystemMutationVariables
>;
export const MutationRejectFailedMessageDocument = {
  kind: 'Document',
  definitions: [
    {
      kind: 'OperationDefinition',
      operation: 'mutation',
      name: { kind: 'Name', value: 'MutationRejectFailedMessage' },
      variableDefinitions: [
        {
          kind: 'VariableDefinition',
          variable: { kind: 'Variable', name: { kind: 'Name', value: 'ids' } },
          type: {
            kind: 'ListType',
            type: {
              kind: 'NonNullType',
              type: { kind: 'NamedType', name: { kind: 'Name', value: 'ID' } },
            },
          },
        },
      ],
      selectionSet: {
        kind: 'SelectionSet',
        selections: [
          {
            kind: 'Field',
            name: { kind: 'Name', value: 'rejectFailedMessage' },
            arguments: [
              {
                kind: 'Argument',
                name: { kind: 'Name', value: 'input' },
                value: {
                  kind: 'ObjectValue',
                  fields: [
                    {
                      kind: 'ObjectField',
                      name: { kind: 'Name', value: 'ids' },
                      value: {
                        kind: 'Variable',
                        name: { kind: 'Name', value: 'ids' },
                      },
                    },
                  ],
                },
              },
            ],
            selectionSet: {
              kind: 'SelectionSet',
              selections: [
                { kind: 'Field', name: { kind: 'Name', value: 'success' } },
              ],
            },
          },
        ],
      },
    },
  ],
} as unknown as DocumentNode<
  MutationRejectFailedMessageMutation,
  MutationRejectFailedMessageMutationVariables
>;
export const MutationRetryFailedMessageDocument = {
  kind: 'Document',
  definitions: [
    {
      kind: 'OperationDefinition',
      operation: 'mutation',
      name: { kind: 'Name', value: 'MutationRetryFailedMessage' },
      variableDefinitions: [
        {
          kind: 'VariableDefinition',
          variable: { kind: 'Variable', name: { kind: 'Name', value: 'id' } },
          type: {
            kind: 'NonNullType',
            type: { kind: 'NamedType', name: { kind: 'Name', value: 'ID' } },
          },
        },
      ],
      selectionSet: {
        kind: 'SelectionSet',
        selections: [
          {
            kind: 'Field',
            name: { kind: 'Name', value: 'retryFailedMessage' },
            arguments: [
              {
                kind: 'Argument',
                name: { kind: 'Name', value: 'input' },
                value: {
                  kind: 'ObjectValue',
                  fields: [
                    {
                      kind: 'ObjectField',
                      name: { kind: 'Name', value: 'id' },
                      value: {
                        kind: 'Variable',
                        name: { kind: 'Name', value: 'id' },
                      },
                    },
                  ],
                },
              },
            ],
            selectionSet: {
              kind: 'SelectionSet',
              selections: [
                { kind: 'Field', name: { kind: 'Name', value: 'success' } },
              ],
            },
          },
        ],
      },
    },
  ],
} as unknown as DocumentNode<
  MutationRetryFailedMessageMutation,
  MutationRetryFailedMessageMutationVariables
>;
export const FailedMessageDocument = {
  kind: 'Document',
  definitions: [
    {
      kind: 'OperationDefinition',
      operation: 'query',
      name: { kind: 'Name', value: 'FailedMessage' },
      variableDefinitions: [
        {
          kind: 'VariableDefinition',
          variable: { kind: 'Variable', name: { kind: 'Name', value: 'id' } },
          type: {
            kind: 'NonNullType',
            type: { kind: 'NamedType', name: { kind: 'Name', value: 'Int' } },
          },
        },
      ],
      selectionSet: {
        kind: 'SelectionSet',
        selections: [
          {
            kind: 'Field',
            name: { kind: 'Name', value: 'failedMessage' },
            arguments: [
              {
                kind: 'Argument',
                name: { kind: 'Name', value: 'id' },
                value: {
                  kind: 'Variable',
                  name: { kind: 'Name', value: 'id' },
                },
              },
            ],
            selectionSet: {
              kind: 'SelectionSet',
              selections: [
                { kind: 'Field', name: { kind: 'Name', value: 'id' } },
                { kind: 'Field', name: { kind: 'Name', value: 'className' } },
                { kind: 'Field', name: { kind: 'Name', value: 'message' } },
                {
                  kind: 'Field',
                  name: { kind: 'Name', value: 'exceptionMessage' },
                },
                {
                  kind: 'Field',
                  name: { kind: 'Name', value: 'backtrace' },
                  selectionSet: {
                    kind: 'SelectionSet',
                    selections: [
                      {
                        kind: 'Field',
                        name: { kind: 'Name', value: 'namespace' },
                      },
                      {
                        kind: 'Field',
                        name: { kind: 'Name', value: 'short_class' },
                      },
                      { kind: 'Field', name: { kind: 'Name', value: 'class' } },
                      { kind: 'Field', name: { kind: 'Name', value: 'type' } },
                      {
                        kind: 'Field',
                        name: { kind: 'Name', value: 'function' },
                      },
                      { kind: 'Field', name: { kind: 'Name', value: 'file' } },
                      { kind: 'Field', name: { kind: 'Name', value: 'line' } },
                      {
                        kind: 'Field',
                        name: { kind: 'Name', value: 'arguments' },
                        selectionSet: {
                          kind: 'SelectionSet',
                          selections: [
                            {
                              kind: 'Field',
                              name: { kind: 'Name', value: 'type' },
                            },
                            {
                              kind: 'Field',
                              name: { kind: 'Name', value: 'value' },
                            },
                          ],
                        },
                      },
                    ],
                  },
                },
                {
                  kind: 'Field',
                  name: { kind: 'Name', value: 'flattenException' },
                  selectionSet: {
                    kind: 'SelectionSet',
                    selections: [
                      {
                        kind: 'Field',
                        name: { kind: 'Name', value: 'message' },
                      },
                      { kind: 'Field', name: { kind: 'Name', value: 'code' } },
                      {
                        kind: 'Field',
                        name: { kind: 'Name', value: 'previous' },
                        selectionSet: {
                          kind: 'SelectionSet',
                          selections: [
                            {
                              kind: 'Field',
                              name: { kind: 'Name', value: 'message' },
                            },
                            {
                              kind: 'Field',
                              name: { kind: 'Name', value: 'code' },
                            },
                            {
                              kind: 'Field',
                              name: { kind: 'Name', value: 'class' },
                            },
                            {
                              kind: 'Field',
                              name: { kind: 'Name', value: 'statusCode' },
                            },
                            {
                              kind: 'Field',
                              name: { kind: 'Name', value: 'statusText' },
                            },
                            {
                              kind: 'Field',
                              name: { kind: 'Name', value: 'headers' },
                            },
                            {
                              kind: 'Field',
                              name: { kind: 'Name', value: 'file' },
                            },
                            {
                              kind: 'Field',
                              name: { kind: 'Name', value: 'line' },
                            },
                          ],
                        },
                      },
                      {
                        kind: 'Field',
                        name: { kind: 'Name', value: 'traceAsString' },
                      },
                      { kind: 'Field', name: { kind: 'Name', value: 'class' } },
                      {
                        kind: 'Field',
                        name: { kind: 'Name', value: 'statusCode' },
                      },
                      {
                        kind: 'Field',
                        name: { kind: 'Name', value: 'statusText' },
                      },
                      {
                        kind: 'Field',
                        name: { kind: 'Name', value: 'headers' },
                      },
                      { kind: 'Field', name: { kind: 'Name', value: 'file' } },
                      { kind: 'Field', name: { kind: 'Name', value: 'line' } },
                    ],
                  },
                },
                { kind: 'Field', name: { kind: 'Name', value: 'date' } },
              ],
            },
          },
        ],
      },
    },
  ],
} as unknown as DocumentNode<FailedMessageQuery, FailedMessageQueryVariables>;
export const FailedMessagesDocument = {
  kind: 'Document',
  definitions: [
    {
      kind: 'OperationDefinition',
      operation: 'query',
      name: { kind: 'Name', value: 'FailedMessages' },
      variableDefinitions: [
        {
          kind: 'VariableDefinition',
          variable: { kind: 'Variable', name: { kind: 'Name', value: 'max' } },
          type: {
            kind: 'NonNullType',
            type: { kind: 'NamedType', name: { kind: 'Name', value: 'Int' } },
          },
        },
      ],
      selectionSet: {
        kind: 'SelectionSet',
        selections: [
          {
            kind: 'Field',
            name: { kind: 'Name', value: 'failedMessages' },
            arguments: [
              {
                kind: 'Argument',
                name: { kind: 'Name', value: 'max' },
                value: {
                  kind: 'Variable',
                  name: { kind: 'Name', value: 'max' },
                },
              },
            ],
            selectionSet: {
              kind: 'SelectionSet',
              selections: [
                { kind: 'Field', name: { kind: 'Name', value: 'id' } },
                { kind: 'Field', name: { kind: 'Name', value: 'className' } },
                {
                  kind: 'Field',
                  name: { kind: 'Name', value: 'exceptionMessage' },
                },
                { kind: 'Field', name: { kind: 'Name', value: 'date' } },
              ],
            },
          },
        ],
      },
    },
  ],
} as unknown as DocumentNode<FailedMessagesQuery, FailedMessagesQueryVariables>;
export const MutationAddReservationDocument = {
  kind: 'Document',
  definitions: [
    {
      kind: 'OperationDefinition',
      operation: 'mutation',
      name: { kind: 'Name', value: 'MutationAddReservation' },
      variableDefinitions: [
        {
          kind: 'VariableDefinition',
          variable: {
            kind: 'Variable',
            name: { kind: 'Name', value: 'service' },
          },
          type: {
            kind: 'NonNullType',
            type: {
              kind: 'NamedType',
              name: { kind: 'Name', value: 'String' },
            },
          },
        },
        {
          kind: 'VariableDefinition',
          variable: {
            kind: 'Variable',
            name: { kind: 'Name', value: 'index' },
          },
          type: {
            kind: 'NonNullType',
            type: { kind: 'NamedType', name: { kind: 'Name', value: 'Int' } },
          },
        },
        {
          kind: 'VariableDefinition',
          variable: { kind: 'Variable', name: { kind: 'Name', value: 'name' } },
          type: {
            kind: 'NonNullType',
            type: {
              kind: 'NamedType',
              name: { kind: 'Name', value: 'String' },
            },
          },
        },
      ],
      selectionSet: {
        kind: 'SelectionSet',
        selections: [
          {
            kind: 'Field',
            name: { kind: 'Name', value: 'addReservation' },
            arguments: [
              {
                kind: 'Argument',
                name: { kind: 'Name', value: 'input' },
                value: {
                  kind: 'ObjectValue',
                  fields: [
                    {
                      kind: 'ObjectField',
                      name: { kind: 'Name', value: 'service' },
                      value: {
                        kind: 'Variable',
                        name: { kind: 'Name', value: 'service' },
                      },
                    },
                    {
                      kind: 'ObjectField',
                      name: { kind: 'Name', value: 'index' },
                      value: {
                        kind: 'Variable',
                        name: { kind: 'Name', value: 'index' },
                      },
                    },
                    {
                      kind: 'ObjectField',
                      name: { kind: 'Name', value: 'name' },
                      value: {
                        kind: 'Variable',
                        name: { kind: 'Name', value: 'name' },
                      },
                    },
                  ],
                },
              },
            ],
            selectionSet: {
              kind: 'SelectionSet',
              selections: [
                {
                  kind: 'InlineFragment',
                  typeCondition: {
                    kind: 'NamedType',
                    name: { kind: 'Name', value: 'SuccessOutput' },
                  },
                  selectionSet: {
                    kind: 'SelectionSet',
                    selections: [
                      {
                        kind: 'Field',
                        name: { kind: 'Name', value: 'success' },
                      },
                    ],
                  },
                },
                {
                  kind: 'InlineFragment',
                  typeCondition: {
                    kind: 'NamedType',
                    name: { kind: 'Name', value: 'FailedOutput' },
                  },
                  selectionSet: {
                    kind: 'SelectionSet',
                    selections: [
                      { kind: 'Field', name: { kind: 'Name', value: 'code' } },
                      {
                        kind: 'Field',
                        name: { kind: 'Name', value: 'message' },
                      },
                    ],
                  },
                },
              ],
            },
          },
        ],
      },
    },
  ],
} as unknown as DocumentNode<
  MutationAddReservationMutation,
  MutationAddReservationMutationVariables
>;
export const MutationDeleteReservationDocument = {
  kind: 'Document',
  definitions: [
    {
      kind: 'OperationDefinition',
      operation: 'mutation',
      name: { kind: 'Name', value: 'MutationDeleteReservation' },
      variableDefinitions: [
        {
          kind: 'VariableDefinition',
          variable: {
            kind: 'Variable',
            name: { kind: 'Name', value: 'service' },
          },
          type: {
            kind: 'NonNullType',
            type: {
              kind: 'NamedType',
              name: { kind: 'Name', value: 'String' },
            },
          },
        },
        {
          kind: 'VariableDefinition',
          variable: { kind: 'Variable', name: { kind: 'Name', value: 'name' } },
          type: {
            kind: 'NonNullType',
            type: {
              kind: 'NamedType',
              name: { kind: 'Name', value: 'String' },
            },
          },
        },
        {
          kind: 'VariableDefinition',
          variable: {
            kind: 'Variable',
            name: { kind: 'Name', value: 'index' },
          },
          type: {
            kind: 'NonNullType',
            type: { kind: 'NamedType', name: { kind: 'Name', value: 'Int' } },
          },
        },
      ],
      selectionSet: {
        kind: 'SelectionSet',
        selections: [
          {
            kind: 'Field',
            name: { kind: 'Name', value: 'deleteReservation' },
            arguments: [
              {
                kind: 'Argument',
                name: { kind: 'Name', value: 'input' },
                value: {
                  kind: 'ObjectValue',
                  fields: [
                    {
                      kind: 'ObjectField',
                      name: { kind: 'Name', value: 'service' },
                      value: {
                        kind: 'Variable',
                        name: { kind: 'Name', value: 'service' },
                      },
                    },
                    {
                      kind: 'ObjectField',
                      name: { kind: 'Name', value: 'index' },
                      value: {
                        kind: 'Variable',
                        name: { kind: 'Name', value: 'index' },
                      },
                    },
                    {
                      kind: 'ObjectField',
                      name: { kind: 'Name', value: 'name' },
                      value: {
                        kind: 'Variable',
                        name: { kind: 'Name', value: 'name' },
                      },
                    },
                  ],
                },
              },
            ],
            selectionSet: {
              kind: 'SelectionSet',
              selections: [
                {
                  kind: 'InlineFragment',
                  typeCondition: {
                    kind: 'NamedType',
                    name: { kind: 'Name', value: 'SuccessOutput' },
                  },
                  selectionSet: {
                    kind: 'SelectionSet',
                    selections: [
                      {
                        kind: 'Field',
                        name: { kind: 'Name', value: 'success' },
                      },
                    ],
                  },
                },
                {
                  kind: 'InlineFragment',
                  typeCondition: {
                    kind: 'NamedType',
                    name: { kind: 'Name', value: 'FailedOutput' },
                  },
                  selectionSet: {
                    kind: 'SelectionSet',
                    selections: [
                      { kind: 'Field', name: { kind: 'Name', value: 'code' } },
                      {
                        kind: 'Field',
                        name: { kind: 'Name', value: 'message' },
                      },
                    ],
                  },
                },
              ],
            },
          },
        ],
      },
    },
  ],
} as unknown as DocumentNode<
  MutationDeleteReservationMutation,
  MutationDeleteReservationMutationVariables
>;
export const ReservationsDocument = {
  kind: 'Document',
  definitions: [
    {
      kind: 'OperationDefinition',
      operation: 'query',
      name: { kind: 'Name', value: 'reservations' },
      selectionSet: {
        kind: 'SelectionSet',
        selections: [
          {
            kind: 'Field',
            name: { kind: 'Name', value: 'reservations' },
            selectionSet: {
              kind: 'SelectionSet',
              selections: [
                { kind: 'Field', name: { kind: 'Name', value: 'service' } },
                { kind: 'Field', name: { kind: 'Name', value: 'name' } },
                { kind: 'Field', name: { kind: 'Name', value: 'index' } },
              ],
            },
          },
        ],
      },
    },
  ],
} as unknown as DocumentNode<ReservationsQuery, ReservationsQueryVariables>;
export const MutationRestartServiceDocument = {
  kind: 'Document',
  definitions: [
    {
      kind: 'OperationDefinition',
      operation: 'mutation',
      name: { kind: 'Name', value: 'MutationRestartService' },
      variableDefinitions: [
        {
          kind: 'VariableDefinition',
          variable: {
            kind: 'Variable',
            name: { kind: 'Name', value: 'masterName' },
          },
          type: {
            kind: 'NonNullType',
            type: {
              kind: 'NamedType',
              name: { kind: 'Name', value: 'String' },
            },
          },
        },
        {
          kind: 'VariableDefinition',
          variable: {
            kind: 'Variable',
            name: { kind: 'Name', value: 'instanceName' },
          },
          type: {
            kind: 'NonNullType',
            type: {
              kind: 'NamedType',
              name: { kind: 'Name', value: 'String' },
            },
          },
        },
      ],
      selectionSet: {
        kind: 'SelectionSet',
        selections: [
          {
            kind: 'Field',
            name: { kind: 'Name', value: 'restartService' },
            arguments: [
              {
                kind: 'Argument',
                name: { kind: 'Name', value: 'input' },
                value: {
                  kind: 'ObjectValue',
                  fields: [
                    {
                      kind: 'ObjectField',
                      name: { kind: 'Name', value: 'masterName' },
                      value: {
                        kind: 'Variable',
                        name: { kind: 'Name', value: 'masterName' },
                      },
                    },
                    {
                      kind: 'ObjectField',
                      name: { kind: 'Name', value: 'instanceName' },
                      value: {
                        kind: 'Variable',
                        name: { kind: 'Name', value: 'instanceName' },
                      },
                    },
                  ],
                },
              },
            ],
            selectionSet: {
              kind: 'SelectionSet',
              selections: [
                {
                  kind: 'InlineFragment',
                  typeCondition: {
                    kind: 'NamedType',
                    name: { kind: 'Name', value: 'SuccessOutput' },
                  },
                  selectionSet: {
                    kind: 'SelectionSet',
                    selections: [
                      {
                        kind: 'Field',
                        name: { kind: 'Name', value: 'success' },
                      },
                    ],
                  },
                },
                {
                  kind: 'InlineFragment',
                  typeCondition: {
                    kind: 'NamedType',
                    name: { kind: 'Name', value: 'FailedOutput' },
                  },
                  selectionSet: {
                    kind: 'SelectionSet',
                    selections: [
                      { kind: 'Field', name: { kind: 'Name', value: 'code' } },
                      {
                        kind: 'Field',
                        name: { kind: 'Name', value: 'message' },
                      },
                    ],
                  },
                },
              ],
            },
          },
        ],
      },
    },
  ],
} as unknown as DocumentNode<
  MutationRestartServiceMutation,
  MutationRestartServiceMutationVariables
>;
export const MutationStartServiceDocument = {
  kind: 'Document',
  definitions: [
    {
      kind: 'OperationDefinition',
      operation: 'mutation',
      name: { kind: 'Name', value: 'MutationStartService' },
      variableDefinitions: [
        {
          kind: 'VariableDefinition',
          variable: {
            kind: 'Variable',
            name: { kind: 'Name', value: 'masterName' },
          },
          type: {
            kind: 'NonNullType',
            type: {
              kind: 'NamedType',
              name: { kind: 'Name', value: 'String' },
            },
          },
        },
        {
          kind: 'VariableDefinition',
          variable: {
            kind: 'Variable',
            name: { kind: 'Name', value: 'index' },
          },
          type: { kind: 'NamedType', name: { kind: 'Name', value: 'Int' } },
        },
        {
          kind: 'VariableDefinition',
          variable: {
            kind: 'Variable',
            name: { kind: 'Name', value: 'instanceName' },
          },
          type: {
            kind: 'NonNullType',
            type: {
              kind: 'NamedType',
              name: { kind: 'Name', value: 'String' },
            },
          },
        },
      ],
      selectionSet: {
        kind: 'SelectionSet',
        selections: [
          {
            kind: 'Field',
            name: { kind: 'Name', value: 'startService' },
            arguments: [
              {
                kind: 'Argument',
                name: { kind: 'Name', value: 'input' },
                value: {
                  kind: 'ObjectValue',
                  fields: [
                    {
                      kind: 'ObjectField',
                      name: { kind: 'Name', value: 'masterName' },
                      value: {
                        kind: 'Variable',
                        name: { kind: 'Name', value: 'masterName' },
                      },
                    },
                    {
                      kind: 'ObjectField',
                      name: { kind: 'Name', value: 'index' },
                      value: {
                        kind: 'Variable',
                        name: { kind: 'Name', value: 'index' },
                      },
                    },
                    {
                      kind: 'ObjectField',
                      name: { kind: 'Name', value: 'instanceName' },
                      value: {
                        kind: 'Variable',
                        name: { kind: 'Name', value: 'instanceName' },
                      },
                    },
                  ],
                },
              },
            ],
            selectionSet: {
              kind: 'SelectionSet',
              selections: [
                {
                  kind: 'InlineFragment',
                  typeCondition: {
                    kind: 'NamedType',
                    name: { kind: 'Name', value: 'SuccessOutput' },
                  },
                  selectionSet: {
                    kind: 'SelectionSet',
                    selections: [
                      {
                        kind: 'Field',
                        name: { kind: 'Name', value: 'success' },
                      },
                    ],
                  },
                },
                {
                  kind: 'InlineFragment',
                  typeCondition: {
                    kind: 'NamedType',
                    name: { kind: 'Name', value: 'FailedOutput' },
                  },
                  selectionSet: {
                    kind: 'SelectionSet',
                    selections: [
                      { kind: 'Field', name: { kind: 'Name', value: 'code' } },
                      {
                        kind: 'Field',
                        name: { kind: 'Name', value: 'message' },
                      },
                    ],
                  },
                },
              ],
            },
          },
        ],
      },
    },
  ],
} as unknown as DocumentNode<
  MutationStartServiceMutation,
  MutationStartServiceMutationVariables
>;
export const MutationStopServiceDocument = {
  kind: 'Document',
  definitions: [
    {
      kind: 'OperationDefinition',
      operation: 'mutation',
      name: { kind: 'Name', value: 'MutationStopService' },
      variableDefinitions: [
        {
          kind: 'VariableDefinition',
          variable: {
            kind: 'Variable',
            name: { kind: 'Name', value: 'masterName' },
          },
          type: {
            kind: 'NonNullType',
            type: {
              kind: 'NamedType',
              name: { kind: 'Name', value: 'String' },
            },
          },
        },
        {
          kind: 'VariableDefinition',
          variable: {
            kind: 'Variable',
            name: { kind: 'Name', value: 'instanceName' },
          },
          type: {
            kind: 'NonNullType',
            type: {
              kind: 'NamedType',
              name: { kind: 'Name', value: 'String' },
            },
          },
        },
      ],
      selectionSet: {
        kind: 'SelectionSet',
        selections: [
          {
            kind: 'Field',
            name: { kind: 'Name', value: 'stopService' },
            arguments: [
              {
                kind: 'Argument',
                name: { kind: 'Name', value: 'input' },
                value: {
                  kind: 'ObjectValue',
                  fields: [
                    {
                      kind: 'ObjectField',
                      name: { kind: 'Name', value: 'masterName' },
                      value: {
                        kind: 'Variable',
                        name: { kind: 'Name', value: 'masterName' },
                      },
                    },
                    {
                      kind: 'ObjectField',
                      name: { kind: 'Name', value: 'instanceName' },
                      value: {
                        kind: 'Variable',
                        name: { kind: 'Name', value: 'instanceName' },
                      },
                    },
                  ],
                },
              },
            ],
            selectionSet: {
              kind: 'SelectionSet',
              selections: [
                {
                  kind: 'InlineFragment',
                  typeCondition: {
                    kind: 'NamedType',
                    name: { kind: 'Name', value: 'SuccessOutput' },
                  },
                  selectionSet: {
                    kind: 'SelectionSet',
                    selections: [
                      {
                        kind: 'Field',
                        name: { kind: 'Name', value: 'success' },
                      },
                    ],
                  },
                },
                {
                  kind: 'InlineFragment',
                  typeCondition: {
                    kind: 'NamedType',
                    name: { kind: 'Name', value: 'FailedOutput' },
                  },
                  selectionSet: {
                    kind: 'SelectionSet',
                    selections: [
                      { kind: 'Field', name: { kind: 'Name', value: 'code' } },
                      {
                        kind: 'Field',
                        name: { kind: 'Name', value: 'message' },
                      },
                    ],
                  },
                },
              ],
            },
          },
        ],
      },
    },
  ],
} as unknown as DocumentNode<
  MutationStopServiceMutation,
  MutationStopServiceMutationVariables
>;
export const ContainersDocument = {
  kind: 'Document',
  definitions: [
    {
      kind: 'OperationDefinition',
      operation: 'query',
      name: { kind: 'Name', value: 'containers' },
      selectionSet: {
        kind: 'SelectionSet',
        selections: [
          {
            kind: 'Field',
            name: { kind: 'Name', value: 'containers' },
            selectionSet: {
              kind: 'SelectionSet',
              selections: [
                {
                  kind: 'Field',
                  name: { kind: 'Name', value: 'containerName' },
                },
                { kind: 'Field', name: { kind: 'Name', value: 'masterName' } },
                {
                  kind: 'Field',
                  name: { kind: 'Name', value: 'instanceName' },
                },
                {
                  kind: 'Field',
                  name: { kind: 'Name', value: 'instanceIndex' },
                },
                {
                  kind: 'Field',
                  name: { kind: 'Name', value: 'zfsFilesystemName' },
                },
                { kind: 'Field', name: { kind: 'Name', value: 'time' } },
                { kind: 'Field', name: { kind: 'Name', value: 'uptime' } },
                { kind: 'Field', name: { kind: 'Name', value: 'dockerState' } },
              ],
            },
          },
        ],
      },
    },
  ],
} as unknown as DocumentNode<ContainersQuery, ContainersQueryVariables>;
export const FilesystemsDocument = {
  kind: 'Document',
  definitions: [
    {
      kind: 'OperationDefinition',
      operation: 'query',
      name: { kind: 'Name', value: 'Filesystems' },
      selectionSet: {
        kind: 'SelectionSet',
        selections: [
          {
            kind: 'Field',
            name: { kind: 'Name', value: 'filesystems' },
            selectionSet: {
              kind: 'SelectionSet',
              selections: [
                { kind: 'Field', name: { kind: 'Name', value: 'name' } },
                { kind: 'Field', name: { kind: 'Name', value: 'type' } },
                { kind: 'Field', name: { kind: 'Name', value: 'origin' } },
                { kind: 'Field', name: { kind: 'Name', value: 'mountPoint' } },
                { kind: 'Field', name: { kind: 'Name', value: 'available' } },
                { kind: 'Field', name: { kind: 'Name', value: 'refer' } },
                { kind: 'Field', name: { kind: 'Name', value: 'used' } },
                { kind: 'Field', name: { kind: 'Name', value: 'usedByChild' } },
                {
                  kind: 'Field',
                  name: { kind: 'Name', value: 'usedByDataset' },
                },
                {
                  kind: 'Field',
                  name: { kind: 'Name', value: 'usedByRefreservation' },
                },
                {
                  kind: 'Field',
                  name: { kind: 'Name', value: 'usedBySnapshot' },
                },
                {
                  kind: 'Field',
                  name: { kind: 'Name', value: 'creationTimestamp' },
                },
              ],
            },
          },
        ],
      },
    },
  ],
} as unknown as DocumentNode<FilesystemsQuery, FilesystemsQueryVariables>;
export const ServicesAndInstancesDocument = {
  kind: 'Document',
  definitions: [
    {
      kind: 'OperationDefinition',
      operation: 'query',
      name: { kind: 'Name', value: 'ServicesAndInstances' },
      selectionSet: {
        kind: 'SelectionSet',
        selections: [
          {
            kind: 'Field',
            name: { kind: 'Name', value: 'services' },
            selectionSet: {
              kind: 'SelectionSet',
              selections: [
                { kind: 'Field', name: { kind: 'Name', value: 'name' } },
                {
                  kind: 'Field',
                  name: { kind: 'Name', value: 'containers' },
                  selectionSet: {
                    kind: 'SelectionSet',
                    selections: [
                      {
                        kind: 'Field',
                        name: { kind: 'Name', value: 'containerName' },
                      },
                      {
                        kind: 'Field',
                        name: { kind: 'Name', value: 'instanceName' },
                      },
                      {
                        kind: 'Field',
                        name: { kind: 'Name', value: 'instanceIndex' },
                      },
                    ],
                  },
                },
              ],
            },
          },
        ],
      },
    },
  ],
} as unknown as DocumentNode<
  ServicesAndInstancesQuery,
  ServicesAndInstancesQueryVariables
>;
export const ServicesDocument = {
  kind: 'Document',
  definitions: [
    {
      kind: 'OperationDefinition',
      operation: 'query',
      name: { kind: 'Name', value: 'Services' },
      selectionSet: {
        kind: 'SelectionSet',
        selections: [
          {
            kind: 'Field',
            name: { kind: 'Name', value: 'services' },
            selectionSet: {
              kind: 'SelectionSet',
              selections: [
                { kind: 'Field', name: { kind: 'Name', value: 'name' } },
                { kind: 'Field', name: { kind: 'Name', value: 'image' } },
                { kind: 'Field', name: { kind: 'Name', value: 'command' } },
                {
                  kind: 'Field',
                  name: { kind: 'Name', value: 'labels' },
                  selectionSet: {
                    kind: 'SelectionSet',
                    selections: [
                      { kind: 'Field', name: { kind: 'Name', value: 'name' } },
                      { kind: 'Field', name: { kind: 'Name', value: 'value' } },
                    ],
                  },
                },
                {
                  kind: 'Field',
                  name: { kind: 'Name', value: 'environments' },
                  selectionSet: {
                    kind: 'SelectionSet',
                    selections: [
                      { kind: 'Field', name: { kind: 'Name', value: 'name' } },
                      { kind: 'Field', name: { kind: 'Name', value: 'value' } },
                    ],
                  },
                },
                {
                  kind: 'Field',
                  name: { kind: 'Name', value: 'ports' },
                  selectionSet: {
                    kind: 'SelectionSet',
                    selections: [
                      {
                        kind: 'Field',
                        name: { kind: 'Name', value: 'containerPort' },
                      },
                      {
                        kind: 'Field',
                        name: { kind: 'Name', value: 'hostPort' },
                      },
                      {
                        kind: 'Field',
                        name: { kind: 'Name', value: 'hostIp' },
                      },
                    ],
                  },
                },
                {
                  kind: 'Field',
                  name: { kind: 'Name', value: 'containers' },
                  selectionSet: {
                    kind: 'SelectionSet',
                    selections: [
                      {
                        kind: 'Field',
                        name: { kind: 'Name', value: 'containerName' },
                      },
                      {
                        kind: 'Field',
                        name: { kind: 'Name', value: 'masterName' },
                      },
                      {
                        kind: 'Field',
                        name: { kind: 'Name', value: 'instanceName' },
                      },
                      {
                        kind: 'Field',
                        name: { kind: 'Name', value: 'instanceIndex' },
                      },
                      {
                        kind: 'Field',
                        name: { kind: 'Name', value: 'zfsFilesystemName' },
                      },
                      {
                        kind: 'Field',
                        name: { kind: 'Name', value: 'exposedPorts' },
                      },
                      { kind: 'Field', name: { kind: 'Name', value: 'time' } },
                      {
                        kind: 'Field',
                        name: { kind: 'Name', value: 'uptime' },
                      },
                      {
                        kind: 'Field',
                        name: { kind: 'Name', value: 'dockerState' },
                      },
                      {
                        kind: 'Field',
                        name: { kind: 'Name', value: 'zfsFilesystem' },
                        selectionSet: {
                          kind: 'SelectionSet',
                          selections: [
                            {
                              kind: 'Field',
                              name: { kind: 'Name', value: 'name' },
                            },
                            {
                              kind: 'Field',
                              name: { kind: 'Name', value: 'type' },
                            },
                            {
                              kind: 'Field',
                              name: { kind: 'Name', value: 'origin' },
                            },
                            {
                              kind: 'Field',
                              name: { kind: 'Name', value: 'mountPoint' },
                            },
                            {
                              kind: 'Field',
                              name: { kind: 'Name', value: 'available' },
                            },
                            {
                              kind: 'Field',
                              name: { kind: 'Name', value: 'used' },
                            },
                            {
                              kind: 'Field',
                              name: { kind: 'Name', value: 'usedByChild' },
                            },
                            {
                              kind: 'Field',
                              name: { kind: 'Name', value: 'usedByDataset' },
                            },
                            {
                              kind: 'Field',
                              name: {
                                kind: 'Name',
                                value: 'usedByRefreservation',
                              },
                            },
                            {
                              kind: 'Field',
                              name: { kind: 'Name', value: 'usedBySnapshot' },
                            },
                            {
                              kind: 'Field',
                              name: {
                                kind: 'Name',
                                value: 'creationTimestamp',
                              },
                            },
                          ],
                        },
                      },
                    ],
                  },
                },
              ],
            },
          },
        ],
      },
    },
  ],
} as unknown as DocumentNode<ServicesQuery, ServicesQueryVariables>;
/** All built-in and custom scalars, mapped to their actual values */
export type Scalars = {
  ID: { input: string; output: string };
  String: { input: string; output: string };
  Boolean: { input: boolean; output: boolean };
  Int: { input: number; output: number };
  Float: { input: number; output: number };
  DateTime: { input: any; output: any };
};

export type AddReservationInput = {
  index?: InputMaybe<Scalars['Int']['input']>;
  name: Scalars['String']['input'];
  service: Scalars['String']['input'];
};

export type AddReservationOutput = FailedOutput | SuccessOutput;

export type Command = {
  __typename?: 'Command';
  name: Scalars['String']['output'];
  output: Array<CommandExecutorResult>;
  subCommands: Array<Scalars['String']['output']>;
};

export type CommandExecutorResult = {
  __typename?: 'CommandExecutorResult';
  output: Array<Scalars['String']['output']>;
  subCommand: Scalars['String']['output'];
};

export type Container = {
  __typename?: 'Container';
  containerName: Scalars['String']['output'];
  dockerState?: Maybe<Scalars['String']['output']>;
  exposedPorts: Array<Scalars['Int']['output']>;
  instanceIndex: Scalars['Int']['output'];
  instanceName: Scalars['String']['output'];
  isMaster: Scalars['Boolean']['output'];
  masterName: Scalars['String']['output'];
  time: Scalars['Int']['output'];
  uptime: Scalars['Int']['output'];
  zfsFilesystem?: Maybe<Filesystem>;
  zfsFilesystemName: Scalars['String']['output'];
};

export type DebugTraceCall = {
  __typename?: 'DebugTraceCall';
  arguments: Array<DebugTraceCallArgument>;
  class?: Maybe<Scalars['String']['output']>;
  file?: Maybe<Scalars['String']['output']>;
  function?: Maybe<Scalars['String']['output']>;
  line?: Maybe<Scalars['Int']['output']>;
  namespace?: Maybe<Scalars['String']['output']>;
  short_class?: Maybe<Scalars['String']['output']>;
  type?: Maybe<Scalars['String']['output']>;
};

export type DebugTraceCallArgument = {
  __typename?: 'DebugTraceCallArgument';
  type?: Maybe<Scalars['String']['output']>;
  value?: Maybe<Scalars['String']['output']>;
};

export type DeleteReservationInput = {
  index?: InputMaybe<Scalars['Int']['input']>;
  name: Scalars['String']['input'];
  service: Scalars['String']['input'];
};

export type DeleteReservationOutput = FailedOutput | SuccessOutput;

export type Environment = {
  __typename?: 'Environment';
  name: Scalars['String']['output'];
  value: Scalars['String']['output'];
};

export type ErrorInterface = {
  code: Scalars['String']['output'];
  message: Scalars['String']['output'];
};

export type FailedMessage = {
  __typename?: 'FailedMessage';
  backtrace: Array<DebugTraceCall>;
  className: Scalars['String']['output'];
  date?: Maybe<Scalars['DateTime']['output']>;
  exceptionMessage?: Maybe<Scalars['String']['output']>;
  flattenException?: Maybe<FlattenException>;
  id: Scalars['ID']['output'];
  message: Scalars['String']['output'];
};

export type FailedMessageOutput = {
  __typename?: 'FailedMessageOutput';
  success?: Maybe<Scalars['Boolean']['output']>;
};

export type FailedOutput = ErrorInterface & {
  __typename?: 'FailedOutput';
  code: Scalars['String']['output'];
  message: Scalars['String']['output'];
};

export type Filesystem = {
  __typename?: 'Filesystem';
  available: Scalars['Float']['output'];
  creationTimestamp: Scalars['Int']['output'];
  mountPoint: Scalars['String']['output'];
  name: Scalars['String']['output'];
  origin: Scalars['String']['output'];
  refer: Scalars['Float']['output'];
  type: Scalars['String']['output'];
  used: Scalars['Float']['output'];
  usedByChild: Scalars['Float']['output'];
  usedByDataset: Scalars['Float']['output'];
  usedByRefreservation: Scalars['Float']['output'];
  usedBySnapshot: Scalars['Float']['output'];
};

export type FlattenException = {
  __typename?: 'FlattenException';
  asString?: Maybe<Scalars['String']['output']>;
  class?: Maybe<Scalars['String']['output']>;
  code?: Maybe<Scalars['Int']['output']>;
  file?: Maybe<Scalars['String']['output']>;
  headers: Array<Scalars['String']['output']>;
  line?: Maybe<Scalars['String']['output']>;
  message?: Maybe<Scalars['String']['output']>;
  previous?: Maybe<FlattenException>;
  statusCode?: Maybe<Scalars['Int']['output']>;
  statusText?: Maybe<Scalars['String']['output']>;
  traceAsString?: Maybe<Scalars['String']['output']>;
};

export type ForceDestroyContainerInput = {
  name: Scalars['String']['input'];
};

export type ForceDestroyContainerOutput = FailedOutput | SuccessOutput;

export type ForceDestroyFilesystemInput = {
  name: Scalars['String']['input'];
};

export type ForceDestroyFilesystemOutput = FailedOutput | SuccessOutput;

export type Label = {
  __typename?: 'Label';
  name: Scalars['String']['output'];
  value: Scalars['String']['output'];
};

export type Mutations = {
  __typename?: 'Mutations';
  addReservation: AddReservationOutput;
  deleteReservation: DeleteReservationOutput;
  forceDestroyContainer: ForceDestroyContainerOutput;
  forceDestroyFilesystem: ForceDestroyFilesystemOutput;
  rejectFailedMessage: FailedMessageOutput;
  restartService: RestartServiceOutput;
  retryFailedMessage: FailedMessageOutput;
  startService: StartServiceOutput;
  stopService: StopServiceOutput;
};

export type MutationsAddReservationArgs = {
  input: AddReservationInput;
};

export type MutationsDeleteReservationArgs = {
  input: DeleteReservationInput;
};

export type MutationsForceDestroyContainerArgs = {
  input: ForceDestroyContainerInput;
};

export type MutationsForceDestroyFilesystemArgs = {
  input: ForceDestroyFilesystemInput;
};

export type MutationsRejectFailedMessageArgs = {
  input: RejectFailedMessageInput;
};

export type MutationsRestartServiceArgs = {
  input: RestartServiceInput;
};

export type MutationsRetryFailedMessageArgs = {
  input: RetryFailedMessageInput;
};

export type MutationsStartServiceArgs = {
  input: StartServiceInput;
};

export type MutationsStopServiceArgs = {
  input: StopServiceInput;
};

export type Port = {
  __typename?: 'Port';
  containerPort?: Maybe<Scalars['String']['output']>;
  hostIp?: Maybe<Scalars['String']['output']>;
  hostPort?: Maybe<Scalars['String']['output']>;
};

export type Query = {
  __typename?: 'Query';
  commandByName?: Maybe<Command>;
  commands: Array<Command>;
  containers: Array<Container>;
  failedMessage: FailedMessage;
  failedMessages: Array<FailedMessage>;
  filesystems: Array<Filesystem>;
  reservations: Array<Reservation>;
  services: Array<Service>;
};

export type QueryCommandByNameArgs = {
  commandName: Scalars['String']['input'];
};

export type QueryFailedMessageArgs = {
  id?: InputMaybe<Scalars['Int']['input']>;
};

export type QueryFailedMessagesArgs = {
  max?: InputMaybe<Scalars['Int']['input']>;
};

export type RejectFailedMessageInput = {
  ids?: InputMaybe<Array<Scalars['ID']['input']>>;
};

export type Reservation = {
  __typename?: 'Reservation';
  index: Scalars['Int']['output'];
  name: Scalars['String']['output'];
  service: Scalars['String']['output'];
};

export type RestartServiceInput = {
  index?: InputMaybe<Scalars['Int']['input']>;
  instanceName: Scalars['String']['input'];
  masterName: Scalars['String']['input'];
};

export type RestartServiceOutput = FailedOutput | SuccessOutput;

export type RetryFailedMessageInput = {
  id: Scalars['ID']['input'];
};

export type Service = {
  __typename?: 'Service';
  command?: Maybe<Scalars['String']['output']>;
  containers: Array<Container>;
  environments: Array<Environment>;
  image: Scalars['String']['output'];
  labels: Array<Label>;
  name: Scalars['String']['output'];
  ports: Array<Port>;
};

export type StartServiceInput = {
  index?: InputMaybe<Scalars['Int']['input']>;
  instanceName: Scalars['String']['input'];
  masterName: Scalars['String']['input'];
};

export type StartServiceOutput = FailedOutput | SuccessOutput;

export type StopServiceInput = {
  instanceName: Scalars['String']['input'];
  masterName: Scalars['String']['input'];
};

export type StopServiceOutput = FailedOutput | SuccessOutput;

export type SuccessOutput = {
  __typename?: 'SuccessOutput';
  message?: Maybe<Scalars['String']['output']>;
  success: Scalars['Boolean']['output'];
};
