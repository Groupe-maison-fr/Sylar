import GraphQL from '../GraphQL';

export interface Filesystem{
  name: string
  type: string
  origin: string
  mountPoint: string
  available: number
  refer: number
  used: number
  usedByChild: number
  usedByDataset: number
  usedByRefreservation: number
  usedBySnapshot: number
  creationTimestamp: number

}
export default ():Promise<Filesystem[]> => GraphQL.query(`
    query {
      filesystems {
        name
        type
        origin
        mountPoint
        available
        refer
        used
        usedByChild
        usedByDataset
        usedByRefreservation
        usedBySnapshot
        creationTimestamp
      }
    }`)
  .then((response) => response.json())
  .then((json) => {
    return json.data.filesystems;
  });
