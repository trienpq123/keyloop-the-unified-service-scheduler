# AI Collaboration Narrative

## Identify Problem & Scope
### AI's role
- Helped identify the core problem and scope of the project
- Provided initial ideas and suggestions for the project

### My role
- Made final decisions based on project requirements and constraints
- Ensured alignment with business goals and technical feasibility
- Verified that the chosen approach was maintainable and scalable

## Build WBS - TASK
### AI's role
- Provided initial WBS structure and examples
- Suggested tasks and subtasks for the WBS
- Helped identify potential issues and edge cases

### My role
- Verify each task is clear, measurable, and aligned with the project goals

### Why build WBS - TASK
- if we have WBS before start, it will make easier to track progress and manage resources
- ensured business golds and technical feasibility

## Choosing system design
### AI's role
- Provided comparison between layered modular monolith, repository-heavy, and microservice alternatives
- Suggested architectural patterns and best practices
- Helped identify potential issues and edge cases

### My role
- Made final decisions based on project requirements and constraints
- Ensured alignment with business goals and technical feasibility
- Verified that the chosen approach was maintainable and scalable

### Why choose design system - modular monolith
The project focuses on customer booking in the system. If we choose microservices, it reduces operational and transaction complexity. On the other hand, if we choose repositories, introducing repository interfaces may be necessary when multiple persistence implementations exist. Based on these factors, I choose the layered modular monolith architecture, which is easier to maintain and develop.

## Build base API response & error message
### AI's role
- Provided initial API response structure and examples
- Suggested fields and data types for the response
- Helped identify potential issues and edge cases

### My role
- Let make idea build base API response and send to AI, then review and refine
- Let make idea build error message and send to AI, then review and refine

### Why build base api repsonse & error message
- Unified at all type message API success, error, and validation
- Let make all developer maintain and easier development implement or feature

## Write code based on tasks
### AI's role
- For each task, provide code implementation based on the task description
- Follow best practices and coding standards

### My role
- Review and refine the code
- Ensure code is clear, maintainable, and aligned with the project goals
- Verify that the code is working as expected

### Why write code based on tasks
- Make sure code is clear, maintainable, and aligned with the project goals
- Verify that the code is working as expected
- Ensure that the code is easy to understand and maintain