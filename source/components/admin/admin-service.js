export default function ($http, appConfig) {
    function getImages() {
        return $http.get(`${appConfig.apiUrl}/images`)
        .then(function (response) {
            return response.data;
        });
    }

    function getEvents() {
        return $http.get(`${appConfig.apiUrl}/events`)
        .then(function (response) {
            return response.data;
        });
    }

    function getForms() {
        return $http.get(`${appConfig.apiUrl}/form`)
        .then(function (response) {
            return response.data;
        });
    }

    function getPastEvents() {
        return $http.get(`${appConfig.apiUrl}/past-events`)
        .then(function (response) {
            return response.data;
        });
    }

    function getLocations() {
        return $http.get(`${appConfig.apiUrl}/location`)
        .then(function (response) {
            return response.data;
        });
    }

    function getStaged(type, contentId) {
        return $http.get(`${appConfig.apiUrl}/staged/${type}/${contentId}`)
        .then(function (response) {
            return response.data;
        });
    }

    function getRevisions(type, contentId) {
        return $http.get(`${appConfig.apiUrl}/revisions/${type}/${contentId}`)
        .then(function (response) {
            return response.data;
        });
    }

    function getRevision(id) {
        return $http.get(`${appConfig.apiUrl}/revision/${id}`)
        .then(function (response) {
            return response.data;
        });
    }

    function approveRevision(id) {
        return $http.put(`${appConfig.apiUrl}/revision/${id}/approve`)
        .then(function (response) {
            return response.data;
        });
    }

    function rejectRevision(id) {
        return $http.put(`${appConfig.apiUrl}/revision/${id}/reject`)
        .then(function (response) {
            return response.data;
        });
    }

    function removeStaged(id) {
        return $http.delete(`${appConfig.apiUrl}/staged/${id}`)
        .then(function (response) {
            return response.data;
        });
    }

    function getUsers() {
        return $http.get(`${appConfig.apiUrl}/users`)
        .then(function (response) {
            return response.data;
        });
    }
    
    function getUser(userId) {
        return $http.get(`${appConfig.apiUrl}/user/${userId}`)
        .then(function (response) {
            return response.data;
        });
    }

    function updateUser(obj) {
        return $http.put(`${appConfig.apiUrl}/user/${obj.id}`, obj)
        .then(function (response) {
            return response.data;
        });
    }

    function removeRsvp(eventId) {
        return $http.delete(`${appConfig.apiUrl}/rsvp/${eventId}`)
        .then(function (response) {
            return response.data;
        });
    }

    function saveRsvp(eventObj) {
        return $http.put(`${appConfig.apiUrl}/rsvp/eventId`, eventObj)
        .then(function (response) {
            return response.data;
        });
    }

    function viewRsvp(eventId) {
        return $http.get(`${appConfig.apiUrl}/rsvp/${eventId}`)
        .then(function (response) {
            return response.data;
        });
    }

    function addUser(obj) {
        return $http.post(`${appConfig.apiUrl}/user`, obj)
        .then(function (response) {
            return response.data;
        });
    }

    function deleteUser(userId) {
        return $http.delete(`${appConfig.apiUrl}/user/${userId}`)
        .then(function (response) {
            return response.data;
        });
    }

    function uploadImage(file) {
        var fd = new FormData();
        fd.append('file', file);
        return $http.post(`${appConfig.apiUrl}/upload/image`, fd, {
            transformRequest: angular.identity,
            headers: {'Content-Type': undefined}
        })
        .then(function (response) {
            return response.data;
        });
    }

    function updateLocations(obj) {
        return $http.put(`${appConfig.apiUrl}/location`, obj)
        .then(function (response) {
            return response.data;
        });
    }

    function updateHome(obj) {
        return $http.put(`${appConfig.apiUrl}/home`, obj)
        .then(function (response) {
            return response.data[0];
        });
    }

    function deleteForm(url) {
        return $http.delete(`${appConfig.apiUrl}/form/${url}`)
        .then(function (response) {
            return response.data[0];
        });
    }

    return {
        getImages: getImages,
        getLocations: getLocations,
        getStaged: getStaged,
        getRevisions: getRevisions,
        getRevision: getRevision,
        removeStaged: removeStaged,
        approveRevision: approveRevision,
        rejectRevision: rejectRevision,
        getUsers: getUsers,
        getUser: getUser,
        updateUser: updateUser,
        addUser: addUser,
        deleteUser: deleteUser,
        uploadImage: uploadImage,
        updateHome: updateHome,
        updateLocations: updateLocations,
        getEvents: getEvents,
        getPastEvents: getPastEvents,
        getForms: getForms,
        removeRsvp: removeRsvp,
        saveRsvp: saveRsvp,
        viewRsvp: viewRsvp,
        deleteForm: deleteForm
    };
}