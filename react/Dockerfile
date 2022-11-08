FROM node:16-alpine AS base
WORKDIR /app

FROM base AS deps
COPY package.json yarn.lock ./
RUN yarn install --production

FROM base AS build
COPY --from=deps /app/node_modules ./node_modules
COPY . .
RUN yarn build

FROM nginx:1.23.2-alpine AS server
WORKDIR /usr/share/nginx/html
# default.conf
COPY default.conf.template /etc/nginx/templates/default.conf.template
COPY 40-index-envsubst.sh /docker-entrypoint.d/
COPY --from=build /app/build .
