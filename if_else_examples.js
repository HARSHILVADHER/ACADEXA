// JavaScript If-Else Condition Examples

console.log("=== JavaScript If-Else Examples ===\n");

// 1. Basic If-Else Statement
console.log("1. Basic If-Else:");
let age = 18;

if (age >= 18) {
    console.log("You are an adult.");
} else {
    console.log("You are a minor.");
}

// 2. If-Else If-Else Chain
console.log("\n2. If-Else If-Else Chain:");
let score = 85;

if (score >= 90) {
    console.log("Grade: A");
} else if (score >= 80) {
    console.log("Grade: B");
} else if (score >= 70) {
    console.log("Grade: C");
} else if (score >= 60) {
    console.log("Grade: D");
} else {
    console.log("Grade: F");
}

// 3. Nested If-Else
console.log("\n3. Nested If-Else:");
let weather = "sunny";
let temperature = 25;

if (weather === "sunny") {
    if (temperature > 20) {
        console.log("Perfect day for outdoor activities!");
    } else {
        console.log("Sunny but a bit cold.");
    }
} else {
    if (temperature > 15) {
        console.log("Not sunny, but warm enough to go out.");
    } else {
        console.log("Stay indoors today.");
    }
}

// 4. Multiple Conditions with Logical Operators
console.log("\n4. Multiple Conditions:");
let username = "admin";
let password = "123456";

if (username === "admin" && password === "123456") {
    console.log("Login successful!");
} else if (username !== "admin") {
    console.log("Invalid username!");
} else {
    console.log("Invalid password!");
}

// 5. Ternary Operator (Short form of if-else)
console.log("\n5. Ternary Operator:");
let number = 15;
let result = number % 2 === 0 ? "Even" : "Odd";
console.log(`${number} is ${result}`);

// 6. Checking Data Types
console.log("\n6. Data Type Checking:");
let value = "Hello";

if (typeof value === "string") {
    console.log("It's a string!");
} else if (typeof value === "number") {
    console.log("It's a number!");
} else if (typeof value === "boolean") {
    console.log("It's a boolean!");
} else {
    console.log("Unknown data type!");
}

// 7. Array Length Check
console.log("\n7. Array Operations:")
let students = ["Alice", "Bob", "Charlie"];

if (students.length > 0) {
    console.log(`There are ${students.length} students in the class.`);
    if (students.length > 5) {
        console.log("Large class size!");
    } else {
        console.log("Small to medium class size.");
    }
} else {
    console.log("No students in the class.");
}

// 8. Object Property Checking
console.log("\n8. Object Property Checking:");
let user = {
    name: "John",
    email: "john@example.com",
    isActive: true
};

if (user.hasOwnProperty("name") && user.name) {
    console.log(`User name: ${user.name}`);
} else {
    console.log("User name not found!");
}

if (user.isActive) {
    console.log("User is active");
} else {
    console.log("User is inactive");
}

// 9. Number Range Checking
console.log("\n9. Number Range Checking:");
let testScore = 75;

if (testScore < 0 || testScore > 100) {
    console.log("Invalid score! Score should be between 0 and 100.");
} else if (testScore >= 90) {
    console.log("Excellent performance!");
} else if (testScore >= 70) {
    console.log("Good performance!");
} else if (testScore >= 50) {
    console.log("Average performance.");
} else {
    console.log("Needs improvement.");
}

// 10. Day of Week Example
console.log("\n10. Day of Week:");
let dayNumber = new Date().getDay();
let dayName;

if (dayNumber === 0) {
    dayName = "Sunday";
} else if (dayNumber === 1) {
    dayName = "Monday";
} else if (dayNumber === 2) {
    dayName = "Tuesday";
} else if (dayNumber === 3) {
    dayName = "Wednesday";
} else if (dayNumber === 4) {
    dayName = "Thursday";
} else if (dayNumber === 5) {
    dayName = "Friday";
} else if (dayNumber === 6) {
    dayName = "Saturday";
}

console.log(`Today is ${dayName}`);

// 11. Function with If-Else
console.log("\n11. Function with If-Else:");
function checkEligibility(age, hasLicense) {
    if (age >= 18) {
        if (hasLicense) {
            return "Eligible to drive";
        } else {
            return "Eligible but needs license";
        }
    } else {
        return "Not eligible to drive";
    }
}

console.log(checkEligibility(20, true));
console.log(checkEligibility(17, false));

// 12. Switch Statement Alternative using If-Else
console.log("\n12. Menu Selection:");
let menuChoice = 2;

if (menuChoice === 1) {
    console.log("You selected: Create New File");
} else if (menuChoice === 2) {
    console.log("You selected: Open File");
} else if (menuChoice === 3) {
    console.log("You selected: Save File");
} else if (menuChoice === 4) {
    console.log("You selected: Exit");
} else {
    console.log("Invalid menu choice!");
}

console.log("\n=== End of Examples ===");