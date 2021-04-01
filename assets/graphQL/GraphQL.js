const query = (queryPayload, headers = []) => fetch('/graphql/', {
    method: 'post',
    headers: {
        Accept: 'application/json, text/plain, */*',
        'Content-Type': 'application/json',
        ...headers
    },
    body: JSON.stringify({ query: queryPayload })
});

const mutation = (mutationPayload, headers = [], files = []) => {
    if (files.length === 0) {
        return fetch('/graphql/', {
            method: 'post',
            headers: {
                Accept: 'application/json, text/plain, */*',
                'Content-Type': 'application/json',
                ...headers
            },
            body: JSON.stringify({ query: mutationPayload })
        });
    }

    const data = new FormData();
    data.append('query', mutationPayload);
    files.forEach((file) => data.append('files[]', file));

    return fetch('/graphql/', {
        method: 'post',
        headers: {
            Accept: 'application/json, text/plain, */*',
            ...headers
        },
        body: data
    });
};

export default {
    query,
    mutation
};
