const introduction = `
    In this study, you will be working with cookies, which are small pieces of data stored by websites in your browser. 
    Cookies can store information such as your preferences, session data, or tracking data to enhance your web experience. 
    Cookie management software, such as browser extensions, allows users to create, modify, and delete these cookies to control their privacy and browsing data. 
    In this study, you will complete a series of tasks related to managing cookies, such as creating, updating, and deleting them. 
    These tasks will help evaluate how effectively the browser extension handles cookie management and its ease of use.

    Please carefully follow the instructions for each task. Good luck!
`;

const disclaimer = `
    Please note: Some tasks may involve multiple steps depending on the extension you are using. 
    This is due to differences in how the extensions handle cookie creation and modification. 
    You are responsible for figuring out how to complete the tasks using the assigned extension.
`;

const tasks = [
    // Combined Creation/Modification Task
    {
        number: 1,
        description: "Create a cookie named `sessionID` with the value `abc123`. If setting an expiration, make sure it's more than a few seconds so it can be verified when you submit the task.",
        category: "Cookie Creation"
    },

    // True/False Cookie Reading Task
    { 
        number: 2, 
        description: "True or false: The cookie named `userPreferences` has the value `darkMode=true`.", 
        category: "Cookie Reading", 
        type: "true-false", 
        correctAnswer: true, 
        initialCookies: [{ name: "userPreferences", value: "darkMode=true" }] 
    },

    // Cookie Update Task
    { 
        number: 3, 
        description: "Update the value of the cookie named `trackingConsent` from `yes` to `no`.", 
        category: "Cookie Update", 
        initialCookies: [{ name: "trackingConsent", value: "yes" }] 
    },
    
    // Cookie Deletion Task
    { 
        number: 4, 
        description: "Delete the cookie named `adConsent`.", 
        category: "Cookie Deletion", 
        initialCookies: [{ name: "adConsent", value: "granted" }] 
    },

    // New Update Task
    { 
        number: 5, 
        description: "Update the value of the cookie named `promoCode` from `DISCOUNT10` to `DISCOUNT20`.", 
        category: "Cookie Update", 
        initialCookies: [{ name: "promoCode", value: "DISCOUNT10" }] 
    }
];

