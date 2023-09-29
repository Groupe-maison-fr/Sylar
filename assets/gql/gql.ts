/* eslint-disable */
import * as types from './graphql';
import { TypedDocumentNode as DocumentNode } from '@graphql-typed-document-node/core';

/**
 * Map of all GraphQL operations in the project.
 *
 * This map has several performance disadvantages:
 * 1. It is not tree-shakeable, so it will include all operations in the project.
 * 2. It is not minifiable, so the string of a GraphQL query will be multiple times inside the bundle.
 * 3. It does not support dead code elimination, so it will add unused operations.
 *
 * Therefore it is highly recommended to use the babel or swc plugin for production.
 */
const documents = {
  '\n        mutation MutationForceDestroyContainer($name: String!) {\n          forceDestroyContainer(input: { name: $name }) {\n            ... on SuccessOutput {\n              success\n            }\n            ... on FailedOutput {\n              code\n            }\n          }\n        }\n      ':
    types.MutationForceDestroyContainerDocument,
  '\n        mutation MutationForceDestroyFilesystem($name: String!) {\n          forceDestroyFilesystem(input: { name: $name }) {\n            ... on SuccessOutput {\n              success\n            }\n            ... on FailedOutput {\n              code\n              message\n            }\n          }\n        }\n      ':
    types.MutationForceDestroyFilesystemDocument,
  '\n        mutation MutationRejectFailedMessage($ids: [ID!]) {\n          rejectFailedMessage(input: { ids: $ids }) {\n            success\n          }\n        }\n      ':
    types.MutationRejectFailedMessageDocument,
  '\n        mutation MutationRetryFailedMessage($id: ID!) {\n          retryFailedMessage(input: { id: $id }) {\n            success\n          }\n        }\n      ':
    types.MutationRetryFailedMessageDocument,
  '\n        query FailedMessage($id: Int!) {\n          failedMessage(id: $id) {\n            id\n            className\n            message\n            exceptionMessage\n            backtrace {\n              namespace\n              short_class\n              class\n              type\n              function\n              file\n              line\n              arguments {\n                type\n                value\n              }\n            }\n            flattenException {\n              message\n              code\n              previous {\n                message\n                code\n                class\n                statusCode\n                statusText\n                headers\n                file\n                line\n              }\n              traceAsString\n              class\n              statusCode\n              statusText\n              headers\n              file\n              line\n            }\n            date\n          }\n        }\n      ':
    types.FailedMessageDocument,
  '\n        query FailedMessages($max: Int!) {\n          failedMessages(max: $max) {\n            id\n            className\n            exceptionMessage\n            date\n          }\n        }\n      ':
    types.FailedMessagesDocument,
  '\n        mutation MutationAddReservation(\n          $service: String!\n          $index: Int!\n          $name: String!\n        ) {\n          addReservation(\n            input: { service: $service, index: $index, name: $name }\n          ) {\n            ... on SuccessOutput {\n              success\n            }\n            ... on FailedOutput {\n              code\n              message\n            }\n          }\n        }\n      ':
    types.MutationAddReservationDocument,
  '\n        mutation MutationDeleteReservation(\n          $service: String!\n          $name: String!\n          $index: Int!\n        ) {\n          deleteReservation(\n            input: { service: $service, index: $index, name: $name }\n          ) {\n            ... on SuccessOutput {\n              success\n            }\n            ... on FailedOutput {\n              code\n              message\n            }\n          }\n        }\n      ':
    types.MutationDeleteReservationDocument,
  '\n        query reservations {\n          reservations {\n            service\n            name\n            index\n          }\n        }\n      ':
    types.ReservationsDocument,
  '\n        mutation MutationRestartService(\n          $masterName: String!\n          $instanceName: String!\n        ) {\n          restartService(\n            input: { masterName: $masterName, instanceName: $instanceName }\n          ) {\n            ... on SuccessOutput {\n              success\n            }\n            ... on FailedOutput {\n              code\n              message\n            }\n          }\n        }\n      ':
    types.MutationRestartServiceDocument,
  '\n        mutation MutationStartService(\n          $masterName: String!\n          $index: Int\n          $instanceName: String!\n        ) {\n          startService(\n            input: {\n              masterName: $masterName\n              index: $index\n              instanceName: $instanceName\n            }\n          ) {\n            ... on SuccessOutput {\n              success\n            }\n            ... on FailedOutput {\n              code\n              message\n            }\n          }\n        }\n      ':
    types.MutationStartServiceDocument,
  '\n        mutation MutationStopService(\n          $masterName: String!\n          $instanceName: String!\n        ) {\n          stopService(\n            input: { masterName: $masterName, instanceName: $instanceName }\n          ) {\n            ... on SuccessOutput {\n              success\n            }\n            ... on FailedOutput {\n              code\n              message\n            }\n          }\n        }\n      ':
    types.MutationStopServiceDocument,
  '\n        query containers {\n          containers {\n            containerName\n            masterName\n            instanceName\n            instanceIndex\n            zfsFilesystemName\n            time\n            uptime\n            dockerState\n          }\n        }\n      ':
    types.ContainersDocument,
  '\n        query Filesystems {\n          filesystems {\n            name\n            type\n            origin\n            mountPoint\n            available\n            refer\n            used\n            usedByChild\n            usedByDataset\n            usedByRefreservation\n            usedBySnapshot\n            creationTimestamp\n          }\n        }\n      ':
    types.FilesystemsDocument,
  '\n        query ServicesAndInstances {\n          services {\n            name\n            containers {\n              containerName\n              instanceName\n              instanceIndex\n            }\n          }\n        }\n      ':
    types.ServicesAndInstancesDocument,
  '\n        query Services {\n          services {\n            name\n            image\n            command\n            labels {\n              name\n              value\n            }\n            environments {\n              name\n              value\n            }\n            ports {\n              containerPort\n              hostPort\n              hostIp\n            }\n            containers {\n              containerName\n              masterName\n              instanceName\n              instanceIndex\n              zfsFilesystemName\n              exposedPorts\n              time\n              uptime\n              dockerState\n              zfsFilesystem {\n                name\n                type\n                origin\n                mountPoint\n                available\n                used\n                usedByChild\n                usedByDataset\n                usedByRefreservation\n                usedBySnapshot\n                creationTimestamp\n              }\n            }\n          }\n        }\n      ':
    types.ServicesDocument,
};

/**
 * The graphql function is used to parse GraphQL queries into a document that can be used by GraphQL clients.
 *
 *
 * @example
 * ```ts
 * const query = graphql(`query GetUser($id: ID!) { user(id: $id) { name } }`);
 * ```
 *
 * The query argument is unknown!
 * Please regenerate the types.
 */
export function graphql(source: string): unknown;

/**
 * The graphql function is used to parse GraphQL queries into a document that can be used by GraphQL clients.
 */
export function graphql(
  source: '\n        mutation MutationForceDestroyContainer($name: String!) {\n          forceDestroyContainer(input: { name: $name }) {\n            ... on SuccessOutput {\n              success\n            }\n            ... on FailedOutput {\n              code\n            }\n          }\n        }\n      ',
): (typeof documents)['\n        mutation MutationForceDestroyContainer($name: String!) {\n          forceDestroyContainer(input: { name: $name }) {\n            ... on SuccessOutput {\n              success\n            }\n            ... on FailedOutput {\n              code\n            }\n          }\n        }\n      '];
/**
 * The graphql function is used to parse GraphQL queries into a document that can be used by GraphQL clients.
 */
export function graphql(
  source: '\n        mutation MutationForceDestroyFilesystem($name: String!) {\n          forceDestroyFilesystem(input: { name: $name }) {\n            ... on SuccessOutput {\n              success\n            }\n            ... on FailedOutput {\n              code\n              message\n            }\n          }\n        }\n      ',
): (typeof documents)['\n        mutation MutationForceDestroyFilesystem($name: String!) {\n          forceDestroyFilesystem(input: { name: $name }) {\n            ... on SuccessOutput {\n              success\n            }\n            ... on FailedOutput {\n              code\n              message\n            }\n          }\n        }\n      '];
/**
 * The graphql function is used to parse GraphQL queries into a document that can be used by GraphQL clients.
 */
export function graphql(
  source: '\n        mutation MutationRejectFailedMessage($ids: [ID!]) {\n          rejectFailedMessage(input: { ids: $ids }) {\n            success\n          }\n        }\n      ',
): (typeof documents)['\n        mutation MutationRejectFailedMessage($ids: [ID!]) {\n          rejectFailedMessage(input: { ids: $ids }) {\n            success\n          }\n        }\n      '];
/**
 * The graphql function is used to parse GraphQL queries into a document that can be used by GraphQL clients.
 */
export function graphql(
  source: '\n        mutation MutationRetryFailedMessage($id: ID!) {\n          retryFailedMessage(input: { id: $id }) {\n            success\n          }\n        }\n      ',
): (typeof documents)['\n        mutation MutationRetryFailedMessage($id: ID!) {\n          retryFailedMessage(input: { id: $id }) {\n            success\n          }\n        }\n      '];
/**
 * The graphql function is used to parse GraphQL queries into a document that can be used by GraphQL clients.
 */
export function graphql(
  source: '\n        query FailedMessage($id: Int!) {\n          failedMessage(id: $id) {\n            id\n            className\n            message\n            exceptionMessage\n            backtrace {\n              namespace\n              short_class\n              class\n              type\n              function\n              file\n              line\n              arguments {\n                type\n                value\n              }\n            }\n            flattenException {\n              message\n              code\n              previous {\n                message\n                code\n                class\n                statusCode\n                statusText\n                headers\n                file\n                line\n              }\n              traceAsString\n              class\n              statusCode\n              statusText\n              headers\n              file\n              line\n            }\n            date\n          }\n        }\n      ',
): (typeof documents)['\n        query FailedMessage($id: Int!) {\n          failedMessage(id: $id) {\n            id\n            className\n            message\n            exceptionMessage\n            backtrace {\n              namespace\n              short_class\n              class\n              type\n              function\n              file\n              line\n              arguments {\n                type\n                value\n              }\n            }\n            flattenException {\n              message\n              code\n              previous {\n                message\n                code\n                class\n                statusCode\n                statusText\n                headers\n                file\n                line\n              }\n              traceAsString\n              class\n              statusCode\n              statusText\n              headers\n              file\n              line\n            }\n            date\n          }\n        }\n      '];
/**
 * The graphql function is used to parse GraphQL queries into a document that can be used by GraphQL clients.
 */
export function graphql(
  source: '\n        query FailedMessages($max: Int!) {\n          failedMessages(max: $max) {\n            id\n            className\n            exceptionMessage\n            date\n          }\n        }\n      ',
): (typeof documents)['\n        query FailedMessages($max: Int!) {\n          failedMessages(max: $max) {\n            id\n            className\n            exceptionMessage\n            date\n          }\n        }\n      '];
/**
 * The graphql function is used to parse GraphQL queries into a document that can be used by GraphQL clients.
 */
export function graphql(
  source: '\n        mutation MutationAddReservation(\n          $service: String!\n          $index: Int!\n          $name: String!\n        ) {\n          addReservation(\n            input: { service: $service, index: $index, name: $name }\n          ) {\n            ... on SuccessOutput {\n              success\n            }\n            ... on FailedOutput {\n              code\n              message\n            }\n          }\n        }\n      ',
): (typeof documents)['\n        mutation MutationAddReservation(\n          $service: String!\n          $index: Int!\n          $name: String!\n        ) {\n          addReservation(\n            input: { service: $service, index: $index, name: $name }\n          ) {\n            ... on SuccessOutput {\n              success\n            }\n            ... on FailedOutput {\n              code\n              message\n            }\n          }\n        }\n      '];
/**
 * The graphql function is used to parse GraphQL queries into a document that can be used by GraphQL clients.
 */
export function graphql(
  source: '\n        mutation MutationDeleteReservation(\n          $service: String!\n          $name: String!\n          $index: Int!\n        ) {\n          deleteReservation(\n            input: { service: $service, index: $index, name: $name }\n          ) {\n            ... on SuccessOutput {\n              success\n            }\n            ... on FailedOutput {\n              code\n              message\n            }\n          }\n        }\n      ',
): (typeof documents)['\n        mutation MutationDeleteReservation(\n          $service: String!\n          $name: String!\n          $index: Int!\n        ) {\n          deleteReservation(\n            input: { service: $service, index: $index, name: $name }\n          ) {\n            ... on SuccessOutput {\n              success\n            }\n            ... on FailedOutput {\n              code\n              message\n            }\n          }\n        }\n      '];
/**
 * The graphql function is used to parse GraphQL queries into a document that can be used by GraphQL clients.
 */
export function graphql(
  source: '\n        query reservations {\n          reservations {\n            service\n            name\n            index\n          }\n        }\n      ',
): (typeof documents)['\n        query reservations {\n          reservations {\n            service\n            name\n            index\n          }\n        }\n      '];
/**
 * The graphql function is used to parse GraphQL queries into a document that can be used by GraphQL clients.
 */
export function graphql(
  source: '\n        mutation MutationRestartService(\n          $masterName: String!\n          $instanceName: String!\n        ) {\n          restartService(\n            input: { masterName: $masterName, instanceName: $instanceName }\n          ) {\n            ... on SuccessOutput {\n              success\n            }\n            ... on FailedOutput {\n              code\n              message\n            }\n          }\n        }\n      ',
): (typeof documents)['\n        mutation MutationRestartService(\n          $masterName: String!\n          $instanceName: String!\n        ) {\n          restartService(\n            input: { masterName: $masterName, instanceName: $instanceName }\n          ) {\n            ... on SuccessOutput {\n              success\n            }\n            ... on FailedOutput {\n              code\n              message\n            }\n          }\n        }\n      '];
/**
 * The graphql function is used to parse GraphQL queries into a document that can be used by GraphQL clients.
 */
export function graphql(
  source: '\n        mutation MutationStartService(\n          $masterName: String!\n          $index: Int\n          $instanceName: String!\n        ) {\n          startService(\n            input: {\n              masterName: $masterName\n              index: $index\n              instanceName: $instanceName\n            }\n          ) {\n            ... on SuccessOutput {\n              success\n            }\n            ... on FailedOutput {\n              code\n              message\n            }\n          }\n        }\n      ',
): (typeof documents)['\n        mutation MutationStartService(\n          $masterName: String!\n          $index: Int\n          $instanceName: String!\n        ) {\n          startService(\n            input: {\n              masterName: $masterName\n              index: $index\n              instanceName: $instanceName\n            }\n          ) {\n            ... on SuccessOutput {\n              success\n            }\n            ... on FailedOutput {\n              code\n              message\n            }\n          }\n        }\n      '];
/**
 * The graphql function is used to parse GraphQL queries into a document that can be used by GraphQL clients.
 */
export function graphql(
  source: '\n        mutation MutationStopService(\n          $masterName: String!\n          $instanceName: String!\n        ) {\n          stopService(\n            input: { masterName: $masterName, instanceName: $instanceName }\n          ) {\n            ... on SuccessOutput {\n              success\n            }\n            ... on FailedOutput {\n              code\n              message\n            }\n          }\n        }\n      ',
): (typeof documents)['\n        mutation MutationStopService(\n          $masterName: String!\n          $instanceName: String!\n        ) {\n          stopService(\n            input: { masterName: $masterName, instanceName: $instanceName }\n          ) {\n            ... on SuccessOutput {\n              success\n            }\n            ... on FailedOutput {\n              code\n              message\n            }\n          }\n        }\n      '];
/**
 * The graphql function is used to parse GraphQL queries into a document that can be used by GraphQL clients.
 */
export function graphql(
  source: '\n        query containers {\n          containers {\n            containerName\n            masterName\n            instanceName\n            instanceIndex\n            zfsFilesystemName\n            time\n            uptime\n            dockerState\n          }\n        }\n      ',
): (typeof documents)['\n        query containers {\n          containers {\n            containerName\n            masterName\n            instanceName\n            instanceIndex\n            zfsFilesystemName\n            time\n            uptime\n            dockerState\n          }\n        }\n      '];
/**
 * The graphql function is used to parse GraphQL queries into a document that can be used by GraphQL clients.
 */
export function graphql(
  source: '\n        query Filesystems {\n          filesystems {\n            name\n            type\n            origin\n            mountPoint\n            available\n            refer\n            used\n            usedByChild\n            usedByDataset\n            usedByRefreservation\n            usedBySnapshot\n            creationTimestamp\n          }\n        }\n      ',
): (typeof documents)['\n        query Filesystems {\n          filesystems {\n            name\n            type\n            origin\n            mountPoint\n            available\n            refer\n            used\n            usedByChild\n            usedByDataset\n            usedByRefreservation\n            usedBySnapshot\n            creationTimestamp\n          }\n        }\n      '];
/**
 * The graphql function is used to parse GraphQL queries into a document that can be used by GraphQL clients.
 */
export function graphql(
  source: '\n        query ServicesAndInstances {\n          services {\n            name\n            containers {\n              containerName\n              instanceName\n              instanceIndex\n            }\n          }\n        }\n      ',
): (typeof documents)['\n        query ServicesAndInstances {\n          services {\n            name\n            containers {\n              containerName\n              instanceName\n              instanceIndex\n            }\n          }\n        }\n      '];
/**
 * The graphql function is used to parse GraphQL queries into a document that can be used by GraphQL clients.
 */
export function graphql(
  source: '\n        query Services {\n          services {\n            name\n            image\n            command\n            labels {\n              name\n              value\n            }\n            environments {\n              name\n              value\n            }\n            ports {\n              containerPort\n              hostPort\n              hostIp\n            }\n            containers {\n              containerName\n              masterName\n              instanceName\n              instanceIndex\n              zfsFilesystemName\n              exposedPorts\n              time\n              uptime\n              dockerState\n              zfsFilesystem {\n                name\n                type\n                origin\n                mountPoint\n                available\n                used\n                usedByChild\n                usedByDataset\n                usedByRefreservation\n                usedBySnapshot\n                creationTimestamp\n              }\n            }\n          }\n        }\n      ',
): (typeof documents)['\n        query Services {\n          services {\n            name\n            image\n            command\n            labels {\n              name\n              value\n            }\n            environments {\n              name\n              value\n            }\n            ports {\n              containerPort\n              hostPort\n              hostIp\n            }\n            containers {\n              containerName\n              masterName\n              instanceName\n              instanceIndex\n              zfsFilesystemName\n              exposedPorts\n              time\n              uptime\n              dockerState\n              zfsFilesystem {\n                name\n                type\n                origin\n                mountPoint\n                available\n                used\n                usedByChild\n                usedByDataset\n                usedByRefreservation\n                usedBySnapshot\n                creationTimestamp\n              }\n            }\n          }\n        }\n      '];

export function graphql(source: string) {
  return (documents as any)[source] ?? {};
}

export type DocumentType<TDocumentNode extends DocumentNode<any, any>> =
  TDocumentNode extends DocumentNode<infer TType, any> ? TType : never;
