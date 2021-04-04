export function encodeSubscription(subscription) {
    return Buffer.from(JSON.stringify(subscription)).toString("base64")
}

export function decodeSubscription(
    subscriptionString
) {
    try {
        const obj = JSON.parse(
            Buffer.from(subscriptionString, "base64").toString("ascii")
        )
        if (!isSubscription(obj)) {
            throw new TypeError("Invalid Push Subscription object")
        }

        return obj
    } catch (err) {
        if (err instanceof SyntaxError && !subscriptionString) {
            console.error(
                `${Env.Subscription} not set, please open the web app to subscribe`
            )
        } else {
            console.error(err)
        }

        return null
    }
}

export function isSubscription(obj) {
    if (!obj || typeof obj !== "object") return false

    if (obj?.hasOwnProperty("endpoint") && obj?.hasOwnProperty("keys")) {
        return true
    }

    return false
}