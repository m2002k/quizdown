FROM node:22-alpine3.20
WORKDIR /app
ENV NODE_ENV development
COPY package*.json ./
RUN npm install
COPY . .
ARG PORT=5173
ENV PORT $PORT
EXPOSE $PORT
CMD npm run dev -- --host --port $PORT