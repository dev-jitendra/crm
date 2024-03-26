



import Base64 from 'js-base64';


class WebSocketManager {

    
    constructor(config) {
        
        this.config = config;

        
        this.subscribeQueue = [];

        
        this.isConnected = false;

        
        this.connection = null;

        
        this.url = '';

        
        this.protocolPart = '';

        const url = this.config.get('webSocketUrl');

        if (url) {
            if (url.indexOf('wss:
                this.url = url.substring(6);
                this.protocolPart = 'wss:
            }
            else {
                this.url = url.substring(5);
                this.protocolPart = 'ws:
            }
        }
        else {
            const siteUrl = this.config.get('siteUrl') || '';

            if (siteUrl.indexOf('https:
                this.url = siteUrl.substring(8);
                this.protocolPart = 'wss:
            }
            else {
                this.url = siteUrl.substring(7);
                this.protocolPart = 'ws:
            }

            if (~this.url.indexOf('/')) {
                this.url = this.url.replace(/\/$/, '');
            }

            const port = this.protocolPart === 'wss:

            const si = this.url.indexOf('/');

            if (~si) {
                this.url = this.url.substring(0, si) + ':' + port;
            }
            else {
                this.url += ':' + port;
            }

            if (this.protocolPart === 'wss:
                this.url += '/wss';
            }
        }
    }

    
    connect(auth, userId) {
        const authArray = Base64.decode(auth).split(':');

        const authToken = authArray[1];

        let url = this.protocolPart + this.url;

        url += '?authToken=' + authToken + '&userId=' + userId;

        try {
            this.connection = new ab.Session(
                url,
                () => {
                    this.isConnected = true;

                    this.subscribeQueue.forEach(item => {
                        this.subscribe(item.category, item.callback);
                    });

                    this.subscribeQueue = [];
                },
                e => {
                    if (e === ab.CONNECTION_CLOSED) {
                        this.subscribeQueue = [];
                    }

                    if (e === ab.CONNECTION_LOST || e === ab.CONNECTION_UNREACHABLE) {
                        setTimeout(() => this.connect(auth, userId), 3000);
                    }
                },
                {skipSubprotocolCheck: true}
            );
        }
        catch (e) {
            console.error(e.message);

            this.connection = null;
        }
    }

    
    subscribe(category, callback) {
        if (!this.connection) {
            return;
        }

        if (!this.isConnected) {
            this.subscribeQueue.push({
                category: category,
                callback: callback,
            });

            return;
        }

        try {
            this.connection.subscribe(category, callback);
        }
        catch (e) {
            if (e.message) {
                console.error(e.message);
            }
            else {
                console.error("WebSocket: Could not subscribe to "+category+".");
            }
        }
    }

    
    unsubscribe(category, callback) {
        if (!this.connection) {
            return;
        }

        this.subscribeQueue = this.subscribeQueue.filter(item => {
            return item.category !== category && item.callback !== callback;
        });

        try {
            this.connection.unsubscribe(category, callback);
        }
        catch (e) {
            if (e.message) {
                console.error(e.message);
            }
            else {
                console.error("WebSocket: Could not unsubscribe from "+category+".");
            }
        }
    }

    
    close() {
        if (!this.connection) {
            return;
        }

        try {
            this.connection.close();
        }
        catch (e) {
            console.error(e.message);
        }

        this.isConnected = false;
    }
}

export default WebSocketManager;
