



class BroadcastChannel {

    constructor() {
        this.object = null;

        if (window.BroadcastChannel) {
            this.object = new window.BroadcastChannel('app');
        }
    }

    
    postMessage(message) {
        if (!this.object) {
            return;
        }

        this.object.postMessage(message);
    }

    

    
    subscribe(callback) {
        if (!this.object) {
            return;
        }

        this.object.addEventListener('message', callback);
    }
}

export default BroadcastChannel;
