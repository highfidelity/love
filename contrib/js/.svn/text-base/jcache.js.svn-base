// Copyright (c) 2010, Andres Clari <andres@andresclari.com>
// 
// jCache is distributed under the New BSD License
// 
// Redistribution and use in source and binary forms, with or without
// modification, are permitted provided that the following conditions are met:
//     * Redistributions of source code must retain the above copyright
//       notice, this list of conditions and the following disclaimer.
//     * Redistributions in binary form must reproduce the above copyright
//       notice, this list of conditions and the following disclaimer in the
//       documentation and/or other materials provided with the distribution.
//     * Neither the name of Andres Clari nor the
//       names of its contributors may be used to endorse or promote products
//       derived from this software without specific prior written permission.
// 
// THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND
// ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
// WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
// DISCLAIMED. IN NO EVENT SHALL ANDRES CLARI BE LIABLE FOR ANY
// DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
// (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
// LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND
// ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
// (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
// SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.

var jCache = {
    // Sets a cache for @name with @data that will expire
    // after @exp hours.
    // Returns true if successful
    set: function(name, data, exp) {
        // Create a timestamp for @name
        $.jStorage.set(jCache.getStampString(name), new Date().getTime());
        
        // Create a expiration for @name with @exp
        $.jStorage.set(jCache.getExpirationString(name), exp);
        
        // Store @data in the new cache
        var store = $.jStorage.set(jCache.getNameString(name), data);
        
        // Give some feedback of the process
        if (store) return true;
        else return false;
    },
    
    // Retrieve cached data for @name
    get: function(name) {        
        return $.jStorage.get(jCache.getNameString(name), false);
    },
    
    // Get timestamp for @name cache
    getTimestamp: function(name) {
        return $.jStorage.get(jCache.getStampString(name));
    },
    
    // Remove cached @name and all the additional keys linked to it
    remove: function(name) {
        $.jStorage.deleteKey(jCache.getNameString(name));
        $.jStorage.deleteKey(jCache.getExpirationString(name));
        $.jStorage.deleteKey(jCache.getStampString(name));
    },
    
    // Reset the cache
    reset: function() {
        $.jStorage.flush();
    },
    
    // Check if the cache @name exists, and if it's expired
    isValid: function(name) {
        var ret = false;
        
        // Check if there's an object with this key with a null value
        var store = $.jStorage.storageObj();
    	for (var key in store) {
    		if (key === name) {
    			if (!store[key]) ret = false;
    		}
    	}
    	if (!ret) return ret;
        
        if ($.jStorage.get(jCache.getNameString(name), false) !== false) ret = true;
        else return ret;
        
        if (jCache.isExpired(name)) ret = false;
        return ret;
    },
    
    // Gets the expiration value for @name
    getExpiration: function(name) {
        return $.jStorage.get(jCache.getExpirationString(name));
    },
    
    // Check if @name is expired
    isExpired: function(name) {  
        var time = new Date().getTime();
        var cacheStamp = $.jStorage.get(jCache.getStampString(name));
        var exp = jCache.getExpiration(name);
        var min = 1000*60;
                
        var time_diff = Math.ceil((time - cacheStamp) / (min));
        if (time_diff > exp) return true;
        else return false;
    },
    
    // Prepares key with jCache info attached
    getNameString: function(name) {
        return ('jCache-' + name);
    },
    
    // Prepares a key with jCache info plus stamp attached
    getStampString: function(name) {
        return ('jCache-' + name + '-stamp');
    },
    
    // Prepares a key with jCache info plus expiration attached
    getExpirationString: function(name) {
        return ('jCache-' + name + '-expiration');
    }
};
